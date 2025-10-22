<?php

namespace App\Models\Ticket;

use App\Enum\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentActivity extends Model
{
    use HasFactory;
    protected $table = 'view_report_agent_activity';

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
                $join->on('st.team_id', 'view_report_agent_activity.user_id');
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
                $join->on('st.user_id', 'view_report_agent_activity.user_id');
                $join->whereIn('st.role', ['agent', 'agent_escalation']);
                $join->where('st.company_id', $companyId);
            });
        }
    }

}
