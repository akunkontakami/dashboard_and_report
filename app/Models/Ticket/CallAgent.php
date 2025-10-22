<?php

namespace App\Models\Ticket;

use App\Enum\Role;
use App\Models\Inbound\InboundTeam;
use App\Models\Outbound\OutboundTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CallAgent extends Model
{
    use HasFactory;
    protected $table = 'view_report_call_agent';

    public function scopeFilterAgent($query, $userRole, $userId, $companyId, $category, $escalationType)
    {
        if (!in_array($userRole, [Role::Admin, Role::BA])) {
            $relation = 'view_escalation_user_team as st';
            $role = ['agent_escalation'];
            if (!str_contains($escalationType, 'escalation')) {
                if ($category == 'inbound') {
                    $relation = 'view_inbound_teams as st';
                    $role = ['agent', 'agent_escalation'];
                } else if ($category == 'outbound') {
                    $relation = 'view_outbound_teams as st';
                    $role = ['agent', 'agent_escalation'];
                }
            }
            $query->join($relation, function ($join) use ($role, $userId, $companyId) {
                $join->on('st.team_id', 'view_report_call_agent.agent_id');
                $join->whereIn('st.team_role', $role);
                $join->where('st.user_id', $userId);
                $join->where('st.company_id', $companyId);
            });
        } else {
            $relation = 'user_helpdesk as st';
            if ($category == 'outbound') {
                $relation = 'user_marketing_campaigns as st';
            }
            $query->join($relation, function ($join) use ($companyId) {
                $join->on('st.user_id', 'view_report_call_agent.agent_id');
                $join->whereIn('st.role', ['agent', 'agent_escalation']);
                $join->where('st.company_id', $companyId);
            });
        }
    }

    public function scopeJoinSpv($query, $userRole,$companyId, $category, $escalationType)
    {
        $relation = 'view_escalation_user_team as sp';
        $role = ['spv_escalation'];
        if (!str_contains($escalationType, 'escalation')) {
            if ($category == 'inbound') {
                $relation = 'view_inbound_teams as sp';
                $role = ['spv'];
            } else if ($category == 'outbound') {
                $relation = 'view_outbound_teams as sp';
                $role = ['spv'];
            }
        }
        $query->leftJoin($relation, function ($join) use ($role, $companyId) {
            $join->on('sp.team_id', 'view_report_call_agent.agent_id');
            $join->whereIn('sp.user_role', $role);
            $join->where('sp.company_id', $companyId);
        });
        $query->leftJoin('company_users', function ($join) use($role) {
            $join->on('company_users.user_id', 'sp.user_id');
            $join->on('company_users.company_id', 'sp.company_id');
            $join->whereIn('company_users.role', $role);
        });
    }
}
