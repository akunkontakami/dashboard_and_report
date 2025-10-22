<table border="1">
     <tr>
         <th style="width:150px"><b>Date</b></th>
         <th style="width:150px"><b>Agent</b></th>
         <th style="width:150px"><b>SPV</b></th>
         <th style="width:150px"><b>Incoming Call</b></th>
         <th style="width:150px"><b>Outgoing Call</b></th>
         <th style="width:150px"><b>Missed Call</b></th>
         <th style="width:150px"><b>Callback</b></th>
         <th style="width:150px"><b>Outgoing Campaign</b></th>
         <th style="width:150px"><b>Talk Time</b></th>
         <th style="width:150px"><b>Avg Talktime Per Call</b></th>
     </tr>
     @foreach ($data['items'] as $row)
         <tr>
             <td style="vertical-align: top;">{{ @$row['date'] }}</td>
             <td style="vertical-align: top;">
                {{ @$row['agent_code'] }}
                -
                {{ @$row['agent_name'] }}
            </td>
             <td style="vertical-align: top;">{{ @$row['spv_name'] }}</td>
             <td style="vertical-align: top;">{{ @$row['incoming'] ?: 0 }}</td>
             <td style="vertical-align: top;">{{ @$row['outgoing'] ?: 0 }}</td>
             <td style="vertical-align: top;">{{ @$row['missed'] ?: 0 }}</td>
             <td style="vertical-align: top;">{{ @$row['callback'] ?: 0 }}</td>
             <td style="vertical-align: top;">0</td>
             <td style="vertical-align: top;">{{ @$row['talktime'] }}</td>
             <td style="vertical-align: top;td">{{ @$row['avg_talktime'] }}</td>
         </tr>
     @endforeach
 </table>
 