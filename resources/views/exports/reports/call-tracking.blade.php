<table border="1">
    <tr>
        <th style="width:150px"><b>Agent</b></th>
        <th style="width:150px"><b>SPV</b></th>
        <th style="width:150px"><b>Customer Count</b></th>
        <th style="width:250px"><b>Ticket Count</b></th>
        @foreach ($data['status'] as $status)
            <th style="width:250px"><b>{{ $status['label'] }}</b></th>
        @endforeach
    </tr>
    @foreach ($data['items'] as $row)
        <tr>
            <td style="vertical-align: top;">
                {{ @$row['agent_code'] }}
                -
                {{ @$row['agent_name'] }}
            </td>
            <td style="vertical-align: top;">{{ @$row['spv_name'] }}</td>
            <td style="vertical-align: top;">{{ @$row['total_customer'] }}</td>
            <td style="vertical-align: top;td">{{ @$row['total_ticket'] }}</td>
            @foreach ($data['status'] as $status)
                <td style="vertical-align: top;td">{{ @$row['status'][$status['slug']] ?: 0 }}</td>
            @endforeach
        </tr>
    @endforeach
</table>
