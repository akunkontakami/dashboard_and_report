<?php
namespace App\Service\Utility;

use Illuminate\Support\Facades\DB;

class CompanyUserService
{
     public function findAllUserTeam($user, $type, $role = 'spv')
     {
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $teamsRole = $role == 'spv' ? ['spv', 'spv_escalation'] : ['agent', 'agent_escalation'];
          $userTeamId = [];

          if ($userRole == 'am') {
               if (str_contains($escalationType, 'escalation_')) {
                    $type = str_contains($escalationType, '_inbound') ? 'inbound' : 'outbound';
                    $userTeamId = DB::table('escalation_team_assignments')
                         ->where('company_id', $companyId)
                         ->where('user_id', $userId)
                         ->where('user_role', $userRole)
                         ->where('category', $type)
                         ->whereIn('team_role', $teamsRole)
                         ->pluck('team_id');
               } else {

                    $userTeamId = DB::table($type == 'inbound' ? 'inbound_teams' : 'outbound_teams')
                         ->where('company_id', $companyId)
                         ->where('user_id', $userId)
                         ->where('user_role', $userRole)
                         ->whereIn('team_role', $teamsRole)
                         ->pluck('team_id');
               }

          }

          return DB::table('company_users')
               ->join('users', 'users.id', 'company_users.user_id')
               ->where('company_users.company_id', $companyId)
               ->whereIn('users.role', $teamsRole)
               ->whereIn('company_users.role', $teamsRole)
               ->when($userRole == 'am', fn($query) => $query->whereIn('company_users.user_id', $userTeamId))
               ->where('company_users.status', 'active')
               ->select(['users.id', 'company_users.name', 'users.role', 'company_users.name as value', 'company_users.profile'])
               ->orderBy('company_users.created_at', 'desc')
               ->get();
     }

}