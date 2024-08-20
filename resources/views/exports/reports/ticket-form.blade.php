<table border="1">
    <tr>
        <th style="width:150px"><b>Date</b></th>
        <th style="width:200px"><b>ID Number</b></th>
        <th style="width:200px"><b>Ticket Number</b></th>
        <th style="width:150px"><b>Customer Name</b></th>
        <th style="width:250px"><b>Dynamic Form : Agent I/O</b></th>
        <th style="width:250px"><b>Dynamic Form : Escalation 1</b></th>
        <th style="width:250px"><b>Dynamic Form : Escalation 2</b></th>
        @if (@$data['type'] == 'outbound')
            <th style="width:250px"><b>Insured</b></th>
            <th style="width:250px"><b>Beneficiary</b></th>
        @endif
    </tr>
    @foreach ($data['items'] as $row)
        <tr>
            <td style="vertical-align: top;">
                {{ date('d M Y H:i:s', strtotime($row->created_at)) }}
            </td>
            <td style="vertical-align: top;">{{ $row->number_id }}</td>
            <td style="vertical-align: top;">{{ $row->ticket_number }}</td>
            <td style="vertical-align: top;">{{ $row->customer_name }}</td>
            <td style="width:250px">
                @foreach (@$row->form['all'] ?: [] as $groupName => $group)
                    @foreach ($group as $form)
                        {{ $groupName }} {{ $form->label }} : {{ $form->content }} <br>
                    @endforeach
                @endforeach
                @foreach (@$row->form['inbound_outbound'] ?: [] as $groupName => $group)
                    @foreach ($group as $form)
                        {{ $groupName }} {{ $form->label }} : {{ $form->content }} <br>
                    @endforeach
                @endforeach
            </td>
            <td style="width:250px">
                @foreach (@$row->form['all'] ?: [] as $groupName => $group)
                    @foreach ($group as $form)
                        {{ $groupName }} {{ $form->label }} : {{ $form->content }} <br>
                    @endforeach
                @endforeach
                @foreach (@$row->form['escalation_1'] ?: [] as $groupName => $group)
                    @foreach ($group as $form)
                        {{ $groupName }} {{ $form->label }} : {{ $form->content }} <br>
                    @endforeach
                @endforeach
            </td>
            <td style="width:250px">
                @foreach (@$row->form['all'] ?: [] as $groupName => $group)
                    @foreach ($group as $form)
                        {{ $groupName }} {{ $form->label }} : {{ $form->content }} <br>
                    @endforeach
                @endforeach
                @foreach (@$row->form['escalation_2'] ?: [] as $groupName => $group)
                    @foreach ($group as $form)
                        {{ $groupName }} {{ $form->label }} : {{ $form->content }} <br>
                    @endforeach
                @endforeach
            </td>
            @if (@$data['type'] == 'outbound')
                @if (@$row->insured)
                    <td style="vertical-align: top;">
                        Group Premi : {{ @$row->insured['group_premi'] }} <br>
                        DOB : {{ @$row->insured['dob'] }} <br>
                        Age : {{ @$row->insured['age'] }} <br>
                        Payment Method : {{ @$row->insured['payment_mode'] }} <br>
                        Plan Type : {{ @$row->insured['plan_type'] }} <br>
                        Premi : {{ @$row->insured['plan_premi_value'] }} <br>
                    </td>
                @endif
                <td style="vertical-align: top;">
                    @foreach ($row->beneficiary as $index => $ben)
                        Beneficiary {{ $index + 1 }} <br>
                        Relation : {{ $ben->relation }} <br>
                        Title : {{ $ben->title }} <br>
                        First Name : {{ $ben->first_name }} <br>
                        Last Name : {{ $ben->last_name }} <br>
                        Gender : {{ $ben->gender }} <br>
                        DOB : {{ $ben->dob }} <br>
                        --------------------------------------- <br>
                    @endforeach
                </td>
            @endif
        </tr>
    @endforeach
</table>
