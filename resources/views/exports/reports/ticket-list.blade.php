<table border="1">
    <tr>
        <th style="width:150px"><b>Created Date</b></th>
        <th style="width:150px"><b>Modified Date	</b></th>
        <th style="width:150px"><b>Call Origin</b></th>
        <th style="width:200px"><b>Ticket Number</b></th>
        <th style="width:150px"><b>Customer Name</b></th>
        {{-- <th style="width:150px"><b>Customer Email</b></th> --}}
        <th style="width:250px"><b>Product Category</b></th>
        @if ($data['type'] == 'inbound')
            <th style="width:250px"><b>Help Desk Category</b></th>
        @else
            <th style="width:250px"><b>Marketing Campaign</b></th>
        @endif
        <th style="width:250px"><b>Product Name</b></th>
        <th style="width:250px"><b>Subject</b></th>
        <th style="width:250px"><b>Priority</b></th>
        <th style="width:250px"><b>Transfer To</b></th>
        <th style="width:250px"><b>Ticket SLA Resolution</b></th>
        <th style="width:250px"><b>Ticket SLA Response</b></th>
        <th style="width:250px"><b>Division SLA</b></th>
        <th style="width:200px"><b>Status </b></th>
        @if ($data['type'] == 'outbound')
        <th style="width:200px"><b>Broadcast Response </b></th>
        @endif
        <th style="width:250px"><b>Agent</b></th>
        <th style="width:250px"><b>SPV</b></th>
    </tr>
    @foreach ($data['items'] as $row)
        <tr>
            <td style="vertical-align: top;">
                {{ $row['date'] }}
            </td>
            <td style="vertical-align: top;">
                {{ $row['updated_at'] }}
            </td>
            <td style="vertical-align: top;">{{ @$row['call_origin'] }}</td>
            <td style="vertical-align: top;">{{ @$row['ticket_number'] }}</td>
            <td style="vertical-align: top;">{{ @$row['customer_name'] }}</td>
            {{-- <td style="vertical-align: top;">{{ @$row['customer_email'] }}</td> --}}
            <td style="vertical-align: top;">{{ @$row['product_category'] }}</td>
            @if ($data['type'] == 'inbound')
                <td style="vertical-align: top;">
                    {{ @$row['helpdesk']['name'] ?: @$row['helpdesk_name']}}
                </td>
            @else
                <td style="vertical-align: top;">
                    {{ @$row['campaign']['name'] }}
                </td>
            @endif
            <td style="vertical-align: top;">{{ @$row['product']['name'] ?: @$row['product_name'] }}</td>
            <td style="vertical-align: top;">{{ @$row['subject']['name'] }}</td>
            <td style="vertical-align: top;" class="{{$row['priority_color']}}">
                {{ @$row['priority'] }}
            </td>
            <td style="vertical-align: top;">{{ @$row['escalation_team']['name'] ?: 'No Division' }}</td>
            <td style="vertical-align: top;color: {{@$row['sla_resolution_time_color']}}">
                {{ str_replace('-','',@$row['sla_resolution_time'])}}    
            </td>
            <td style="vertical-align: top;color: {{@$row['sla_response_time_color']}}">
                {{ str_replace('-','',@$row['sla_response_time']) }}    
            </td>
            <td style="vertical-align: top;color: {{@$row['sla_division_color']}}">
                {{ str_replace('-','',@$row['sla_division']) }}    
            </td>
            <td style="vertical-align: top;" class="{{ @$row['status_color'] }}">{{ @$row['status'] }}</td>
            @if ($data['type'] == 'outbound')
            <td style="vertical-align: top;">{{ @$row['is_broadcasted'] ?: 'No' }}</td>
            @endif
            <td style="vertical-align: top;">{{ @$row['agent']['name'] ?: '-' }}</td>
            <td style="vertical-align: top;">{{ @$row['spv']['name'] ?: '-' }}</td>
        </tr>
    @endforeach
</table>
<style>
    .bg-online{
        color: #38A363 !important
    }
    .bg-yellow{
        color: #FEB500 !important
    }
    .bg-offline{
        color: #FE4C4C !important
    }
</style>