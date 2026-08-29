<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Services\NipGenerator;

class EmployeeDataExchangeController extends Controller
{
    private const COMMON_HEADERS = ['nama', 'email'];

    private const HEADERS = [
        'karyawan' => ['id_pengguna', 'nip', 'nama', 'email', 'telepon', 'alamat', 'status_aktif', 'jabatan_divisi', 'id_atasan', 'ktp', 'kk', 'tanggal_masuk', 'npwp', 'tempat_lahir', 'tanggal_lahir', 'status_pernikahan', 'bpjs_ketenagakerjaan', 'nama_gadis_ibu_kandung', 'jumlah_tanggungan', 'foto_ktp', 'tahun'],
        'pengajar' => ['id_pengguna', 'nip', 'nama', 'email', 'telepon', 'alamat', 'status_aktif', 'divisi_akademik', 'kampus_asal', 'dokumen_pelatihan', 'nomor_sertifikat', 'npwp', 'tempat_lahir', 'tanggal_lahir', 'status_pernikahan', 'bpjs_ketenagakerjaan', 'nama_gadis_ibu_kandung', 'jumlah_tanggungan', 'foto_ktp', 'tahun'],
    ];

    private const EXPORT_COLUMNS = [
        'created_at' => 'Timestamp',
        'nama' => 'NAMA LENGKAP',
        'tempat_lahir' => 'TEMPAT LAHIR',
        'tanggal_lahir' => 'TANGGAL LAHIR',
        'alamat' => 'ALAMAT SESUAI KTP',
        'ktp' => 'NIK',
        'npwp' => 'NPWP (JIKA ADA)',
        'bpjs_ketenagakerjaan' => 'NO BPJS KETENAGAKERJAAN',
        'nama_gadis_ibu_kandung' => 'NAMA GADIS IBU KANDUNG',
        'status_pernikahan' => 'STATUS PERKAWINAN',
        'jumlah_tanggungan' => 'JUMLAH TANGGUNGAN (SELAIN ISTRI)',
        'telepon' => 'No Telepon Aktif',
        'email' => 'Email Aktif',
        'foto_ktp' => 'Foto KTP',
        'kk' => 'NO KK',
        'tahun' => 'TAHUN',

        // Kolom tambahan dari sistem, diletakkan setelah kolom Excel asli.
        'id_pengguna' => 'ID PENGGUNA',
        'nip' => 'NIP',
        'status_aktif' => 'STATUS AKTIF',
        'jabatan_divisi' => 'JABATAN / DIVISI',
        'id_atasan' => 'ID ATASAN',
        'divisi_akademik' => 'DIVISI AKADEMIK',
        'kampus_asal' => 'ASAL SEKOLAH / KAMPUS',
        'jenis_kelamin' => 'JENIS KELAMIN',
        'agama' => 'AGAMA',
        'berat_badan' => 'BERAT BADAN',
        'tinggi_badan' => 'TINGGI BADAN',
        'ukuran_baju' => 'UKURAN BAJU',
        'gol_darah' => 'GOLONGAN DARAH',
        'tanggal_masuk' => 'TANGGAL MASUK',
        'tanggal_keluar' => 'TANGGAL KELUAR',
        'dokumen_pelatihan' => 'DOKUMEN PELATIHAN',
        'nomor_sertifikat' => 'NOMOR SERTIFIKAT',
    ];

    public function template(string $role): BinaryFileResponse
    {
        return $this->downloadWorkbook($role, [], true);
    }

    public function export(string $role): BinaryFileResponse
    {
        $rows = Employee::where('peran', $role)->orderBy('nama')->get()
            ->map(fn (Employee $employee) => $this->rowFor($employee, $role))->all();

        return $this->downloadWorkbook($role, $rows);
    }

    public function import(Request $request, string $role)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:5120'],
        ]);

        try {
            $sheet = IOFactory::load($request->file('file')->getRealPath())->getActiveSheet();
            $rows = $sheet->toArray('', true, true, false);
        } catch (\Throwable) {
            return back()->withErrors(['file' => 'Dokumen tidak dapat dibaca. Gunakan template Excel atau CSV yang disediakan.']);
        }

        $headers = array_map(fn ($header) => $this->normalizeHeader($header), array_shift($rows) ?? []);
        $expectedHeaders = self::HEADERS[$role];
        $missing = array_diff(self::COMMON_HEADERS, $headers);

        if ($missing) {
            return back()->withErrors(['file' => 'Kolom wajib tidak ditemukan: '.implode(', ', $missing).'. Unduh template terlebih dahulu.']);
        }

        $data = [];
        $errors = [];
        $seenEmails = [];

        foreach ($rows as $index => $row) {
            $row = array_pad($row, count($headers), null);
            $item = array_filter(array_combine($headers, array_slice($row, 0, count($headers))), fn ($value) => $value !== null && $value !== '');
            $line = $index + 2;

            if ($item === []) {
                continue;
            }

            $item = array_intersect_key($item, array_flip($expectedHeaders));
            $item = $this->normalizeImportedValues($item);
            $item['email'] = strtolower(trim((string) ($item['email'] ?? '')));
            $item['status_aktif'] = strtolower(trim((string) ($item['status_aktif'] ?? 'aktif')));

            $existing = Employee::where('email', $item['email'])->first();
            $validator = Validator::make($item, $this->rules($existing));
            if ($validator->fails()) {
                $errors[] = "Baris {$line}: ".implode(' ', $validator->errors()->all());
            }
            if ($item['email'] !== '' && isset($seenEmails[$item['email']])) {
                $errors[] = "Baris {$line}: email duplikat di dalam dokumen.";
            }
            if ($existing && $existing->peran !== $role) {
                $errors[] = "Baris {$line}: email sudah digunakan untuk peran {$existing->peran}.";
            }

            unset($item['nip'], $item['id_pengguna']);
            $seenEmails[$item['email']] = true;
            $data[] = $item;
        }

        if ($data === []) {
            return back()->withErrors(['file' => 'Dokumen tidak memiliki baris data untuk diimpor.']);
        }
        if ($errors) {
            return back()->withErrors(['file' => array_slice($errors, 0, 10)]);
        }

        DB::transaction(function () use ($data, $role): void {
            foreach ($data as $item) {
                $employee = Employee::where('email', $item['email'])->first();
                if ($employee) {
                    $employee->update($item + ['peran' => $role]);
                    continue;
                }

                $employee = new Employee($item + ['peran' => $role]);
                $employee->nip = app(NipGenerator::class)->generate($employee, $role);
                $employee->save();
            }
        });

        $label = $role === 'karyawan' ? 'karyawan' : 'pengajar';
        $indexRoute = $role === 'karyawan' ? 'admin.employees.index' : 'admin.teachers.index';
        return redirect()->route($indexRoute)
            ->with('success', count($data)." data {$label} berhasil diimpor.");
    }

    private function rules(?Employee $existing): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('employees', 'email')->ignore($existing)],
            'telepon' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'status_aktif' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'jabatan_divisi' => ['nullable', 'string', 'max:255'],
            'id_atasan' => ['nullable', 'string', 'max:255'],
            'ktp' => ['nullable', 'string', 'max:255'],
            'kk' => ['nullable', 'string', 'max:255'],
            'bpjs_ketenagakerjaan' => ['nullable', 'string', 'max:255'],
            'nama_gadis_ibu_kandung' => ['nullable', 'string', 'max:255'],
            'jumlah_tanggungan' => ['nullable', 'integer', 'min:0'],
            'status_pernikahan' => ['nullable', Rule::in(['Menikah', 'Belum Menikah'])],
            'jenis_kelamin' => ['nullable', Rule::in(['Laki-laki', 'Perempuan'])],
            'gol_darah' => ['nullable', Rule::in(['A', 'B', 'AB', 'O'])],
            'ukuran_baju' => ['nullable', Rule::in(['S', 'M', 'L', 'XL', 'XXL', 'XXXL'])],
            'foto_ktp' => ['nullable', 'string', 'max:255'],
            'tahun' => ['nullable', 'integer', 'digits:4'],
            'tanggal_masuk' => ['nullable', 'date'],
            'tanggal_lahir' => ['nullable', 'date'],
            'divisi_akademik' => ['nullable', 'string', 'max:255'],
            'kampus_asal' => ['nullable', 'string', 'max:255'],
            'dokumen_pelatihan' => ['nullable', 'string', 'max:255'],
            'nomor_sertifikat' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function normalizeImportedValues(array $item): array
    {
        if (isset($item['status_pernikahan'])) {
            $status = strtolower(trim((string) $item['status_pernikahan']));
            $item['status_pernikahan'] = match ($status) {
                'menikah', 'kawin', 'married' => 'Menikah',
                'belum menikah', 'belum kawin', 'lajang', 'single', 'unmarried' => 'Belum Menikah',
                default => $item['status_pernikahan'],
            };
        }

        if (isset($item['jenis_kelamin'])) {
            $gender = strtolower(trim((string) $item['jenis_kelamin']));
            $item['jenis_kelamin'] = match ($gender) {
                'l', 'laki-laki', 'laki laki', 'pria', 'male' => 'Laki-laki',
                'p', 'perempuan', 'wanita', 'female' => 'Perempuan',
                default => $item['jenis_kelamin'],
            };
        }

        return $item;
    }

    private function downloadWorkbook(string $role, array $rows, bool $template = false): BinaryFileResponse
    {
        $headers = array_values(self::EXPORT_COLUMNS);
        $lastColumn = Coordinate::stringFromColumnIndex(count($headers));
        $book = new Spreadsheet();
        $sheet = $book->getActiveSheet();
        $title = $role === 'karyawan' ? 'Karyawan' : 'Pengajar';
        $sheet->setTitle($title);
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:'.$lastColumn.'1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        if ($template) {
            $sample = array_fill_keys(array_keys(self::EXPORT_COLUMNS), '');
            $sample['nama'] = 'Contoh Nama';
            $sample['email'] = 'contoh@villamerah.id';
            $sheet->fromArray([array_values($sample)], null, 'A2');
            $sheet->setCellValue('A4', 'Catatan: hapus baris contoh sebelum mengimpor. NIP dibuat otomatis oleh sistem; kolom nama, email, dan status_aktif wajib diisi.');
            $sheet->mergeCells('A4:'.$lastColumn.'4');
        } elseif ($rows) {
            $sheet->fromArray(array_map(fn ($row) => array_values($row), $rows), null, 'A2');
        }

        for ($columnIndex = 1; $columnIndex <= count($headers); $columnIndex++) {
            $column = Coordinate::stringFromColumnIndex($columnIndex);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $filename = ($template ? 'template-' : 'data-').$role.'-'.now()->format('Ymd-His').'.xlsx';
        $path = storage_path('app/private/'.$filename);
        (new Xlsx($book))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    private function rowFor(Employee $employee, string $role): array
    {
        $row = [];
        foreach (array_keys(self::EXPORT_COLUMNS) as $header) {
            $value = match ($header) {
                'created_at' => $employee->created_at,
                'id_pengguna' => $employee->user?->login_id,
                default => $employee->{$header},
            };
            $row[$header] = $value instanceof \DateTimeInterface
                ? $value->format($header === 'created_at' ? 'Y-m-d H:i:s' : 'Y-m-d')
                : $value;
        }
        return $row;
    }

    private function normalizeHeader(mixed $header): string
    {
        $header = strtolower(trim((string) $header));
        $aliases = array_change_key_case(array_flip(self::EXPORT_COLUMNS));

        return $aliases[$header] ?? $header;
    }
}
