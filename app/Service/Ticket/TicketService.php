<?php
namespace App\Service\Ticket;

use App\Models\Ticket\Ticket;
use App\Models\Util\InboundStatus;
use Illuminate\Support\Str;

class TicketService
{
     public function __construct(
          private $model = Ticket::class,
          private $inboundStatus = InboundStatus::class,
     ) {
     }

     public function findAllStatusTicketWithColor($user, $type)
     {
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;

          $tickets = $this->model::query()
               ->leftJoin("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->when($userRole === 'spv_escalation' || str_contains($escalationType, 'escalation'), fn($q) => $q->with([
                    'agentEsca' => fn($query) => $query->where('company_id', $companyId)->select(['user_id', 'user_id as id', 'company_users.name']),
               ]))
               ->when($escalationType && !str_contains($escalationType, 'escalation'), fn($query) => $query->whereNull('tickets.escalation_team_id'))
               ->filterAgent(null, $companyId, $userId, $userRole, $type, $escalationType)
               ->active()
               ->distinct()
               ->when($type == 'outbound', fn($query) => $query->whereRelation('campaign', 'status', 'active'))
               ->select('tickets.status', 'st.status_category', 'tickets.ticket_number','tickets.status_id')
               ->get();

          return $tickets->map(function ($row) {
               $statusColor = in_array($row->status, ['New', 'From Bot', 'From Whatsapp Bot','From Chatbot']) ? 'bg-offline' : match ($row->status_category) {
                    'Closed' => 'bg-online',
                    'Solved' => 'bg-blue',
                    'Open' => 'bg-kuning',
                    'New' => 'bg-offline',
                    default => 'bg-online',
               };
               $statusColor = str_contains(strtolower($row->status), 'closed') ? 'bg-online' : $statusColor;
               $statusColor = str_contains(strtolower($row->status), 'new') ? 'bg-offline' : $statusColor;
               $statusColor = str_contains(strtolower($row->status), 'solved') ? 'bg-blue' : $statusColor;
               $html = "<span class='flex gap-2 items-center justify-center'><span class='h-[10px] w-[10px] rounded-full {$statusColor} block'></span> {$row->status}</span>";
               return [
                    'label' => $row->status,
                    'color' => $statusColor,
                    'html' => $html,
                    'slug' => $row->status_id . "-" . Str::slug($row->status, '_')
               ];
          })
               ->unique()
               ->values();
     }

     
     public function findAllProductTicket($user, $type)
     {
          
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;

          return $this->model::query()
               ->filterAgent(null, $companyId, $userId, $userRole, $type, $escalationType)
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->whereNotNull('tickets.product_name')
               ->active()
               ->when($type == 'outbound', fn($query) => $query->whereRelation('campaign', 'status', 'active'))
               ->distinct()
               ->pluck('tickets.product_name');
     }

     public function findAllInboundStatus($companyId){
          $data = [];
          $masterStatus = $this->findAllInboundStatusWithSub($companyId);
          foreach ($masterStatus as $status) {
               $subs = $status->sub;
               if (!count($subs)) {
                    $data[] = [
                         'id' => $status->id,
                         'label' => $status->name,
                         'slug' => $status->id . "-" . Str::slug($status->name, '_')
                    ];
               }
               foreach ($subs as $sub) {
                    $data[] = [
                         'id' => $sub->id,
                         'label' => "{$status->name} - {$sub->name}",
                         'slug' => $sub->id . "-" . Str::slug($sub->name, '_')
                    ];
               }
          }
          return [
               ...[
                    [
                         "id" => "escalation-in-progress",
                         "label" => "Escalation : In Progress",
                         "slug" => "escalation-in-progress",
                    ], [
                         "id" => "escalation-done",
                         "label" => "Escalation : Closed",
                         "slug" => "escalation-done",
                    ]    
               ],
               ...$data
          ];
     }

     public function findAllInboundStatusWithSub($companyId)
     {
          return $this->inboundStatus::with(['sub:id,name,parent_id,status,status_category,email_customer_content,submit_without_fill,subject'])
               ->where('company_id', $companyId)
               ->oldest('sorting')
               ->select('id', 'parent_id', 'name','status','status_category','email_customer_content','submit_without_fill','subject')
               ->whereNull('parent_id')
               ->get();
     }
}