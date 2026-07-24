<table>
    <thead>
        <tr>
            <th rowspan="2" style="background-color: #59359a; color: #ffffff; text-align: center; font-weight: bold;">ลำดับ</th>
            <th rowspan="2" style="background-color: #59359a; color: #ffffff; text-align: center; font-weight: bold;">ชื่อหน่วยงาน</th>
            <th rowspan="2" style="background-color: #59359a; color: #ffffff; text-align: center; font-weight: bold;">วงเงินที่ได้รับจัดสรร</th>
            
            <th colspan="3" style="background-color: #e65c00; color: #ffffff; text-align: center; font-weight: bold;">เงินงบประมาณ</th>
            <th colspan="3" style="background-color: #d63384; color: #ffffff; text-align: center; font-weight: bold;">เงินรายได้</th>
            <th colspan="4" style="background-color: #198754; color: #ffffff; text-align: center; font-weight: bold;">ผลการใช้จ่ายรวม</th>
            <th colspan="5" style="background-color: #0dcaf0; color: #000000; text-align: center; font-weight: bold;">สถานะโครงการ</th>
        </tr>
        <tr>
            <!-- เงินงบประมาณ -->
            <th style="background-color: #e65c00; color: #ffffff; text-align: center; font-weight: bold;">วงเงินจัดสรร</th>
            <th style="background-color: #e65c00; color: #ffffff; text-align: center; font-weight: bold;">ผลเบิกจ่าย</th>
            <th style="background-color: #e65c00; color: #ffffff; text-align: center; font-weight: bold;">คงเหลือ</th>
            
            <!-- เงินรายได้ -->
            <th style="background-color: #d63384; color: #ffffff; text-align: center; font-weight: bold;">วงเงินจัดสรร</th>
            <th style="background-color: #d63384; color: #ffffff; text-align: center; font-weight: bold;">ผลเบิกจ่าย</th>
            <th style="background-color: #d63384; color: #ffffff; text-align: center; font-weight: bold;">คงเหลือ</th>
            
            <!-- ผลการใช้จ่ายรวม -->
            <th style="background-color: #198754; color: #ffffff; text-align: center; font-weight: bold;">วงเงินทั้งหมด</th>
            <th style="background-color: #198754; color: #ffffff; text-align: center; font-weight: bold;">เบิกจ่ายรวม</th>
            <th style="background-color: #198754; color: #ffffff; text-align: center; font-weight: bold;">คงเหลือรวม</th>
            <th style="background-color: #198754; color: #ffffff; text-align: center; font-weight: bold;">ร้อยละเบิกจ่าย</th>
            
            <!-- สถานะโครงการ -->
            <th style="background-color: #0dcaf0; color: #000000; text-align: center; font-weight: bold;">จำนวน (รวม)</th>
            <th style="background-color: #0dcaf0; color: #000000; text-align: center; font-weight: bold;">แล้วเสร็จ</th>
            <th style="background-color: #0dcaf0; color: #000000; text-align: center; font-weight: bold;">ดำเนินการ</th>
            <th style="background-color: #0dcaf0; color: #000000; text-align: center; font-weight: bold;">ยังไม่ทำ</th>
            <th style="background-color: #0dcaf0; color: #000000; text-align: center; font-weight: bold;">ยกเลิก</th>
        </tr>
    </thead>
    <tbody>
        @php $index = 1; @endphp
        @foreach($reportData as $row)
            <tr>
                <td style="text-align: center;">{{ $row->is_parent ? $index++ : '' }}</td>
                <td style="{{ $row->is_parent ? 'font-weight: bold;' : '' }}">
                    @if(!$row->is_parent) &nbsp;&nbsp;&nbsp;&nbsp; @endif
                    {{ $row->department_name }}
                </td>
                <td style="text-align: right; {{ $row->is_parent ? 'font-weight: bold;' : '' }}">{{ $row->total_allocated }}</td>
                
                <td style="text-align: right;">{{ $row->budget_allocated }}</td>
                <td style="text-align: right;">{{ $row->budget_paid }}</td>
                <td style="text-align: right; color: {{ $row->budget_remaining < 0 ? '#dc3545' : '#000000' }};">{{ $row->budget_remaining }}</td>
                
                <td style="text-align: right;">{{ $row->income_allocated }}</td>
                <td style="text-align: right;">{{ $row->income_paid }}</td>
                <td style="text-align: right; color: {{ $row->income_remaining < 0 ? '#dc3545' : '#000000' }};">{{ $row->income_remaining }}</td>
                
                <td style="text-align: right; font-weight: bold;">{{ $row->total_allocated }}</td>
                <td style="text-align: right; font-weight: bold; color: #198754;">{{ $row->total_paid }}</td>
                <td style="text-align: right; font-weight: bold; color: {{ $row->total_remaining < 0 ? '#dc3545' : '#0d6efd' }};">{{ $row->total_remaining }}</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($row->percent_paid, 2) }}%</td>
                
                <td style="text-align: center; font-weight: bold;">{{ $row->total_projects }}</td>
                <td style="text-align: center; color: #198754;">{{ $row->completed }}</td>
                <td style="text-align: center; color: #ffc107;">{{ $row->processing }}</td>
                <td style="text-align: center; color: #6c757d;">{{ $row->waiting }}</td>
                <td style="text-align: center; color: #dc3545;">{{ $row->cancelled }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold; background-color: #f8f9fa;">รวมทั้งสิ้น:</td>
            <td style="text-align: right; font-weight: bold; background-color: #f8f9fa;">{{ $totals->total_allocated_all }}</td>
            
            <td style="text-align: right; font-weight: bold; background-color: #f8f9fa;">{{ $totals->budget_allocated }}</td>
            <td style="text-align: right; font-weight: bold; background-color: #f8f9fa;">{{ $totals->budget_paid }}</td>
            <td style="text-align: right; font-weight: bold; background-color: #f8f9fa; color: #dc3545;">{{ $totals->budget_remaining }}</td>
            
            <td style="text-align: right; font-weight: bold; background-color: #f8f9fa;">{{ $totals->income_allocated }}</td>
            <td style="text-align: right; font-weight: bold; background-color: #f8f9fa;">{{ $totals->income_paid }}</td>
            <td style="text-align: right; font-weight: bold; background-color: #f8f9fa; color: #dc3545;">{{ $totals->income_remaining }}</td>
            
            <td style="text-align: right; font-weight: bold; background-color: #f8f9fa;">{{ $totals->total_allocated }}</td>
            <td style="text-align: right; font-weight: bold; background-color: #f8f9fa; color: #198754;">{{ $totals->total_paid }}</td>
            <td style="text-align: right; font-weight: bold; background-color: #f8f9fa; color: #0d6efd;">{{ $totals->total_remaining }}</td>
            <td style="text-align: right; font-weight: bold; background-color: #f8f9fa;">{{ number_format($totals->percent_paid, 2) }}%</td>
            
            <td style="text-align: center; font-weight: bold; background-color: #f8f9fa;">{{ $totals->total_projects }}</td>
            <td style="text-align: center; font-weight: bold; background-color: #f8f9fa; color: #198754;">{{ $totals->completed }}</td>
            <td style="text-align: center; font-weight: bold; background-color: #f8f9fa; color: #ffc107;">{{ $totals->processing }}</td>
            <td style="text-align: center; font-weight: bold; background-color: #f8f9fa; color: #6c757d;">{{ $totals->waiting }}</td>
            <td style="text-align: center; font-weight: bold; background-color: #f8f9fa; color: #dc3545;">{{ $totals->cancelled }}</td>
        </tr>
    </tfoot>
</table>