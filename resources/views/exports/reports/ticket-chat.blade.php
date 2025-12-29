<table border="1">
    <tr>
        <th style="width:150px"><b>Date</b></th>
        <th style="width:200px"><b>Ticket Number</b></th>
        <th style="width:150px"><b>Customer Name</b></th>
        <th style="width:250px"><b>Notes to/from Caller</b></th>
        <th style="width:250px"><b>Internal Remarks</b></th>
        <th style="width:250px"><b>Call History</b></th>
        {{-- <th style="width:250px"><b>In App Chat</b></th> --}}
        {{-- <th style="width:250px"><b>WhatsApp</b></th> --}}
    </tr>
    @foreach ($data['items'] as $row)
        <tr>
            <td style="vertical-align: top;">
                {{ date('d M Y H:i:s', strtotime($row->created_at)) }}
            </td>
            <td style="vertical-align: top;">{{ $row->ticket_number }}</td>
            <td style="vertical-align: top;">{{ $row->customer_name }}</td>
            <td style="vertical-align: top;">
                @foreach (@$row->comments['note'] ?: [] as $note)
                    [ {{ $note->name ?: $row->customer_name }} - {{ date('d M Y H:i:s',strtotime($note->created_at)) }} ] : {{ $note->content }} {{ $note->file ? '('.$note->file.')' : '' }} <br>
                @endforeach
            </td>
            <td style="vertical-align: top;">
                @foreach (@$row->comments['remark'] ?: [] as $remark)
                    [ {{ $remark->name ?: $row->customer_name }} - {{ date('d M Y H:i:s',strtotime($remark->created_at)) }} ] : {{ $remark->content }} <br>
                @endforeach
            </td>
            <td style="vertical-align: top;">
                [ {{ date('d M Y H:i:s', strtotime($row->history_date)) }} : {{ $row->status }} -
                {{ $row->agent_name }}]
            </td>
            {{-- <td style="vertical-align: top;width:350px">
                @foreach ($row->messages as $chat)
                    <p>
                        <b>[ {{ $chat->name ?: $row->customer_name }} :
                            {{ date('d M Y H:i:s', strtotime($chat->created_at)) }} ]</b>
                        @if ($chat->message_type == 'location')
                            <a
                                href="https://maps.google.com/maps?q={{ $chat->message->lat }},{{ $chat->message->lng }}">
                                https://maps.google.com/maps?q={{ $chat->message->lat }},{{ $chat->message->lng }}
                            </a>
                        @elseif(in_array($chat->message_type, ['file']))
                            <a href="{{ $chat->message->url }}">
                                {{ $chat->message->url }}
                            </a>
                        @else
                            {{ json_encode($chat->message) }}
                        @endif
                    </p>
                @endforeach
            </td> --}}
            {{-- <td style="vertical-align: top;width:350px">
                @foreach ($row->wa ?: [] as $wa)
                    <p>
                        <b>[ {{ $wa->name ?: $row->customer_name }} :
                            {{ date('d M Y H:i:s', strtotime($wa->created_at)) }} ]</b>
                        @if ($wa->message_type == 'location')
                            <a href="https://maps.google.com/maps?q={{ $wa->message->lat }},{{ $wa->message->lng }}">
                                https://maps.google.com/maps?q={{ $wa->message->lat }},{{ $wa->message->lng }}
                            </a>
                        @elseif(in_array($wa->message_type, ['document', 'video', 'file', 'image', 'sticker']))
                            <a href="{{ $wa->message->url }}">
                                {{ $wa->message->url }}
                            </a>
                        @else
                            {{ json_encode($wa->message) }}
                        @endif
                    </p>
                @endforeach
            </td> --}}
        </tr>
    @endforeach
</table>
