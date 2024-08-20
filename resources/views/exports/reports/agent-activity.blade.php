<table border="1">
    <tr>
        <th style="width:150px"><b>Date</b></th>
        <th style="width:150px"><b>Name</b></th>
        <th style="width:150px"><b>Role</b></th>
        <th style="width:250px"><b>Login Time</b></th>
        <th style="width:250px"><b>Available Time</b></th>
        <th style="width:250px"><b>Talk Time</b></th>
        <th style="width:250px"><b>Offline</b></th>
    </tr>
    @foreach ($data['items'] as $row)
        <tr>
            <td style="vertical-align: top;">{{ @$row['date'] }}</td>
            <td style="vertical-align: top;">
                {{ @$row['name'] }}
            </td>
            <td style="vertical-align: top;">{{ @$row['role'] }}</td>
            <td style="vertical-align: top;td">{{ @$row['login_time'] }}</td>
            <td style="vertical-align: top;td">{{ @$row['available_time'] }}</td>
            <td style="vertical-align: top;td">{{ @$row['talktime'] }}</td>
            <td style="vertical-align: top;td">{{ @$row['offline'] }}</td>
        </tr>
    @endforeach
</table>
