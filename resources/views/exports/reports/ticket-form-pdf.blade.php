<p class="label">Date</p>
<div class="input">
    {{ $row->created_at }}
</div>
<p class="label">ID Number</p>
<div class="input">
    {{ $row->number_id ?: '-' }}
</div>
<p class="label">Ticket Number</p>
<div class="input">
    {{ $row->ticket_number ?: '-' }}
</div>
<p class="label">Customer Name</p>
<div class="input">
    {{ $row->customer_name ?: '-' }}
</div>
{{-- inbound_outbound --}}
<h2 class="group-name" style="margin-top: 20px">Inbound / Outbound</h2>
@foreach (@$row->form['all'] ?: [] as $groupName => $forms)
    <div class="card">
        <h2 class="group-name">{{ $groupName }}</h2>
        @foreach ($forms as $form)
            <p class="label">{{ $form->label }}</p>
            <div class="input">
                {{ $form->content ?: '-' }}
            </div>
        @endforeach
    </div>
@endforeach
@foreach (@$row->form['inbound_outbound'] ?: [] as $groupName => $forms)
    <div class="card">
        <h2 class="group-name">{{ $groupName }}</h2>
        @foreach ($forms as $form)
            <p class="label">{{ $form->label }}</p>
            <div class="input">
                {{ $form->content ?: '-' }}
            </div>
        @endforeach
    </div>
@endforeach
{{-- escalation_1 --}}
<h2 class="group-name" style="margin-top: 20px">Escalation 1</h2>
@foreach (@$row->form['all'] ?: [] as $groupName => $forms)
    <div class="card">
        <h2 class="group-name">{{ $groupName }}</h2>
        @foreach ($forms as $form)
            <p class="label">{{ $form->label }}</p>
            <div class="input">
                {{ $form->content ?: '-' }}
            </div>
        @endforeach
    </div>
@endforeach
@foreach (@$row->form['escalation_1'] ?: [] as $groupName => $forms)
    <div class="card">
        <h2 class="group-name">{{ $groupName }}</h2>
        @foreach ($forms as $form)
            <p class="label">{{ $form->label }}</p>
            <div class="input">
                {{ $form->content ?: '-' }}
            </div>
        @endforeach
    </div>
@endforeach

{{-- escalation_2 --}}
<h2 class="group-name" style="margin-top: 20px">Escalation 2</h2>
@foreach (@$row->form['all'] ?: [] as $groupName => $forms)
    <div class="card">
        <h2 class="group-name">{{ $groupName }}</h2>
        @foreach ($forms as $form)
            <p class="label">{{ $form->label }}</p>
            <div class="input">
                {{ $form->content ?: '-' }}
            </div>
        @endforeach
    </div>
@endforeach
@foreach (@$row->form['escalation_2'] ?: [] as $groupName => $forms)
    <div class="card">
        <h2 class="group-name">{{ $groupName }}</h2>
        @foreach ($forms as $form)
            <p class="label">{{ $form->label }}</p>
            <div class="input">
                {{ $form->content ?: '-' }}
            </div>
        @endforeach
    </div>
@endforeach
@if ($category == 'outbound')
    <div class="card">
        <h2 class="group-name">Insured</h2>
        <p class="label">Group Premi</p>
        <div class="input">
            {{ @$row->insured['group_premi'] ?: '-' }}
        </div>
        <p class="label">DOB</p>
        <div class="input">
            {{ @$row->insured['dob'] ?: '-' }}
        </div>
        <p class="label">Age</p>
        <div class="input">
            {{ @$row->insured['age'] ?: '-' }}
        </div>
        <p class="label">Payment Method</p>
        <div class="input">
            {{ @$row->insured['payment_mode'] ?: '-' }}
        </div>
        <p class="label">Plan Type</p>
        <div class="input">
            {{ @$row->insured['plan_type'] ?: '-' }}
        </div>
        <p class="label">Premi</p>
        <div class="input">
            {{ @$row->insured['plan_premi_value'] ?: '-' }}
        </div>
    </div>

    @foreach($row->beneficiary as $index => $ben)
        <div class="card">
            <h2 class="group-name">Beneficiary {{$index+1}}</h2>
            <p class="label"Relation</p>
            <div class="input">
                {{ $ben?->relation ?: '-' }}
            </div>
            <p class="label">Title</p>
            <div class="input">
                {{ $ben?->title ?: '-' }}
            </div>
            <p class="label"> First Name</p>
            <div class="input">
                {{ $ben?->first_name ?: '-' }}
            </div>
            <p class="label">Last Name</p>
            <div class="input">
                {{ $ben?->last_name ?: '-' }}
            </div>
            <p class="label">Gender</p>
            <div class="input">
                {{ $ben?->gender ?: '-' }}
            </div>
            <p class="label">DOB</p>
            <div class="input">
                {{ $ben?->dob ?: '-' }}
            </div>
        </div>
    @endforeach
@endif
<style>
    .group-name {
        border-bottom: 1px solid #000;
        margin: 0px;
        font-size: 15px;
        padding-bottom: 10px;
        margin-bottom: 5px;
    }

    .card {
        background: #fff;
        border-radius: 10px;
        padding: 15px 20px;
        border: 1px solid #ddd;
        margin-top: 10px;
    }

    .label {
        font-weight: bold;
        margin-bottom: 2px;
        display: inline-block;
        font-size: 14px
    }

    .input {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 10px 15px;
        backface-visibility: #fff;
        font-size: 14px
    }

    body {
        background: #F7F7F7;
        padding: 10px 25px;
    }


    * {
        font-family: sans-serif;
    }


    @page {
        margin: 0px;
    }
</style>
