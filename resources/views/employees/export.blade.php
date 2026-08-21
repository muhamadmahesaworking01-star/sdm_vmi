<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>NIP</th>
            <th>Peran</th>
            <th>Status</th>
            <th>Email</th>
            <th>Telepon</th>
            <th>Alamat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($employees as $employee)
            <tr>
                <td>{{ $employee->nama }}</td>
                <td>{{ $employee->nip }}</td>
                <td>{{ ucfirst($employee->peran) }}</td>
                <td>{{ ucfirst($employee->status_aktif) }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->telepon }}</td>
                <td>{{ $employee->alamat }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
