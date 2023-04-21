<table style="border: 1px solid black; width:100%">
    <tr>
        <th>ID</th>
        <th>CODE</th>
        <th>EMAIL</th>
        <th>NAME</th>
        <th>GROUP NAME</th>
        <th>ADDED DATE</th>
    </tr>
    
    @foreach ($vouchers as $key => $report)
    <tr>
        <td>
            {{ $report->voucher_id }}
        </td>
        <td>
            {{ $report->code }}
        </td>
        <td>
            {{ $report->email }}
        </td>
        <td>
            {{ $report->name }}
        </td>
        <td>
            {{ $report->group_name }}
        </td>
        <td>
            {{ $report->created_at }}
        </td>
    </tr>
    @endforeach
</table>