<?php

namespace Tests\Unit;

use App\Models\Employee;
use PHPUnit\Framework\TestCase;

class EmployeeModelTest extends TestCase
{
    /** @test */
    public function employee_has_expected_fillable_attributes()
    {
        $model = new Employee();
        
        $this->assertContains('gol_darah', $model->getFillable());
        $this->assertContains('tanggal_keluar', $model->getFillable());
    }
}
