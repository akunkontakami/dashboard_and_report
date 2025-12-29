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
        <th style="width:250px">
            <b> @if (!empty($verifications))
                {{ $verifications[0]['verification_name'] }}
                @else
                -
                @endif</b>
        </th>
    @endif
    </tr>

    @foreach ($data['items'] as $row)
    <tr>
        <td>{{ date('d M Y H:i:s', strtotime($row->created_at)) }}</td>
        <td>{{ $row->number_id }}</td>
        <td>{{ $row->ticket_number }}</td>
        <td>{{ $row->customer_name }}</td>

        {{-- Agent I/O --}}
        <td style="width:250px">
            @foreach ($row->form['all'] ?? [] as $groupName => $group)
            @foreach ($group as $form)
            {{ $groupName }} {{ $form->label }} : {{ $form->content }} <br>
            @endforeach
            @endforeach
            @foreach ($row->form['inbound_outbound'] ?? [] as $groupName => $group)
            @foreach ($group as $form)
            {{ $groupName }} {{ $form->label }} : {{ $form->content }} <br>
            @endforeach
            @endforeach
        </td>

        {{-- Escalation 1 --}}
        <td style="width:250px">
            @foreach ($row->form['all'] ?? [] as $groupName => $group)
            @foreach ($group as $form)
            {{ $groupName }} {{ $form->label }} : {{ $form->content }} <br>
            @endforeach
            @endforeach
            @foreach ($row->form['escalation_1'] ?? [] as $groupName => $group)
            @foreach ($group as $form)
            {{ $groupName }} {{ $form->label }} : {{ $form->content }} <br>
            @endforeach
            @endforeach
        </td>

        {{-- Escalation 2 --}}
        <td style="width:250px">
            @foreach ($row->form['all'] ?? [] as $groupName => $group)
            @foreach ($group as $form)
            {{ $groupName }} {{ $form->label }} : {{ $form->content }} <br>
            @endforeach
            @endforeach
            @foreach ($row->form['escalation_2'] ?? [] as $groupName => $group)
            @foreach ($group as $form)
            {{ $groupName }} {{ $form->label }} : {{ $form->content }} <br>
            @endforeach
            @endforeach
        </td>
     @if (@$data['type'] == 'outbound')
            {{-- Insured --}}
            @php
            $insured = is_array($row->insured) ? (object) $row->insured : $row->insured;
            @endphp

            <td>
                @if(!empty($insured))
                Group Premi : {{ $insured->group_premi ?? '-' }} <br>
                DOB : {{ $insured->dob ?? '-' }} <br>
                Age : {{ $insured->age ?? '-' }} <br>
                Payment Method : {{ $insured->payment_mode ?? '-' }} <br>
                Plan Type : {{ $insured->plan_type ?? '-' }} <br>
                Premi : {{ $insured->plan_premi_value ?? '-' }} <br>
                @else
                -
                @endif
            </td>

            {{-- Beneficiary --}}
            <td>
                @if(!empty($row->beneficiary))
                @foreach ($row->beneficiary as $index => $ben)
                Beneficiary {{ $index + 1 }} <br>
                Relation : {{ $ben->relation ?? '-' }} <br>
                Title : {{ $ben->title ?? '-' }} <br>
                First Name : {{ $ben->first_name ?? '-' }} <br>
                Last Name : {{ $ben->last_name ?? '-' }} <br>
                Gender : {{ $ben->gender ?? '-' }} <br>
                DOB : {{ $ben->dob ?? '-' }} <br>
                --------------------------------------- <br>
                @endforeach
                @else
                -
                @endif
            </td>


            {{-- Verif Lengkap --}}
            <td style="width:250px; vertical-align: top;">
                @if (!empty($verifications))
                @foreach ($verifications as $ver)
                <u>{{ $ver['group_name'] }}</u><br>
                @foreach ($ver['fields'] as $f)
                {{ $f['label'] }} : {{ $f['value'] ?: '-' }} <br>
                @endforeach
                <br>
                @endforeach
                @else
                -
                @endif
            </td>
         @endif
    </tr>
    @endforeach
</table>