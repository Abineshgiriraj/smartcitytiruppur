<?php
declare(strict_types=1);

/** Presentation only: destination pages continue to enforce their own access. */
function employee_navigation(array $user): array
{
    $role = (string)($user['role'] ?? '');
    $admin = ['Super Admin','Admin'];
    $manager = ['Super Admin','Admin','Commissioner','Department Head'];
    $global = ['Super Admin','Admin','Commissioner'];
    $officer = in_array($role, ['Field Officer','Officer'], true);
    $groups = [];
    $add = static function (string $group, string $key, string $label, string $icon, array $links) use (&$groups, $user, $role): void {
        $visible = [];
        foreach ($links as $link) {
            [$target,$name,$permission,$roles] = $link;
            if ($roles && !in_array($role,$roles,true)) continue;
            if ($permission !== null && !can_access_permission($user,$permission)) continue;
            $visible[] = [$target,$name];
        }
        if ($visible) $groups[$group][] = ['key'=>$key,'label'=>$label,'icon'=>$icon,'links'=>$visible];
    };
    if ($officer) {
        $add('My Workspace','officer-home','Dashboard','bi-grid-1x2',[['officer-dashboard.php','Dashboard',null,[]]]);
        $add('My Workspace','my-complaints','My Complaints','bi-inbox',[
            ['my-complaints.php','All current',null,[]],
            ['my-complaints.php?status=Assigned','Needs action',null,[]],
            ['my-complaints.php?status=Overdue','Overdue',null,[]],
            ['my-complaints.php?status=TransferredToMe','Transferred to me',null,[]],
            ['my-complaints.php?status=PreviouslyHandled','Previously handled',null,[]],
            ['my-complaints.php?status=In%20Progress','In progress',null,[]],
            ['my-complaints.php?status=Resolved','Resolved',null,[]],
            ['my-complaints.php?status=Closed','Closed',null,[]],
            ['my-complaints.php?status=Reopened','Reopened',null,[]],
            ['legacy-complaint-history.php','Verified historical complaints',null,[]],
        ]);
        $add('My Workspace','officer-feedback','Citizen Feedback','bi-chat-left-heart',[['officer-feedback.php','Feedback',null,[]]]);
        $add('My Workspace','officer-profile','My Profile','bi-person-circle',[['officer-profile.php','Profile',null,[]]]);
        return $groups;
    }
    $add('Workspace','dashboard','Dashboard','bi-grid-1x2-fill',[['dashboard.php','Overview','dashboard.view',[]]]);
    $add('Complaint Management','new','New Complaint','bi-plus-circle',[['../new-complaint.php','Register complaint','complaints.update',[]]]);
    $add('Complaint Management','complaints','All Complaints','bi-inboxes',[
        ['complaints.php','All complaints','complaints.view',[]],
        ['complaints.php?status=Assigned','Assigned','complaints.view',[]],
        ['complaints.php?status=Reopened','Reopened','complaints.view',[]],
        ['complaint-centre.php?view=completed','Closed / resolved','complaints.view',[]],
        ['complaint-centre.php?view=repeated','Potential repeats','complaints.view',[]],
    ]);
    $add('Complaint Management','escalation','Escalation Centre','bi-diagram-3-fill',[
        ['escalation-reports.php','Escalation reports','escalation.view',$manager],
        ['escalation.php','Overdue monitor','escalation.view',$manager],
        ['escalation-audit.php','Configuration audit','masters.view',$admin],
    ]);
    $add('Complaint Management','allocation','Allocation & Review','bi-shuffle',[
        ['routing-analysis.php','Routing & reassignment','masters.view',$admin],
        ['allocation-inspector.php','Allocation inspector','masters.view',$admin],
        ['officer-readiness.php','Mapping readiness','masters.view',$admin],
    ]);
    $add('Complaint Management','feedback','Citizen Feedback','bi-chat-left-text',[['feedback.php','Feedback','feedback.view',[]]]);
    $add('Reports','reports','Reports & Analytics','bi-bar-chart-line',[
        ['icts-reports.php','ICTS reports','reports.view',[]],
        ['reports.php','Data export','reports.view',[]],
        ['complaint-centre.php?view=category','Category','complaints.view',[]],
        ['complaint-centre.php?view=zone','Zone','complaints.view',[]],
        ['complaint-centre.php?view=map','Area coverage','complaints.view',[]],
    ]);
    $add('Reports','performance','Officer Performance','bi-clipboard-data',[
        ['officer-complaint-report.php','Workload and performance','complaints.view',$manager],
        ['legacy-officer-report.php','Verified historical report','complaints.view',$global],
    ]);
    $add('Administration','officers','Officer Management','bi-people',[
        ['officers.php','Officer directory','officers.view',$manager],
        ['masters.php?section=officers','Officer master','masters.view',$admin],
        ['masters.php?section=mappings','Officer mapping','masters.view',$admin],
        ['user-mapping-details.php','Mapping details','masters.view',$admin],
    ]);
    $add('Administration','settings','Master & Settings','bi-sliders',[
        ['masters.php','Master data','masters.view',$admin],
        ['masters.php?section=allocations','Allocation rules','masters.view',$admin],
        ['pdf-policy-review.php','PDF policy review','masters.view',$admin],
        ['officer-approvals.php','Stage approvals','masters.view',$admin],
        ['master-control.php','Advanced masters','masters.view',$admin],
    ]);
    $add('Administration','users','Users & Permissions','bi-person-gear',[
        ['users.php','Users and roles','users.view',$admin],
        ['page-permissions.php','Page permissions','roles.view',$admin],
    ]);
    // Routing analysis exposes complaint data as well as master configuration.
    foreach ($groups as &$items) {
        foreach ($items as &$item) {
            if ($item['key'] === 'allocation' && !can_access_permission($user,'complaints.view')) {
                $item['links'] = array_values(array_filter($item['links'], static fn($link) => $link[0] !== 'routing-analysis.php'));
            }
        }
        unset($item);
    }
    unset($items);
    return $groups;
}

/** Query-specific routes must take precedence over generic routes. */
function employee_navigation_matches(string $target, string $page, array $query): bool
{
    if (basename(explode('?', $target, 2)[0]) !== $page) return false;
    parse_str((string)(parse_url($target, PHP_URL_QUERY) ?: ''), $expected);
    foreach ($expected as $key => $value) {
        if (($query[$key] ?? null) !== $value) return false;
    }
    if (!$expected) {
        if ($page === 'masters.php' && in_array($query['section'] ?? '', ['officers','mappings','allocations'],true)) return false;
        if ($page === 'complaints.php' && in_array($query['status'] ?? '', ['Assigned','Reopened'],true)) return false;
        if ($page === 'my-complaints.php' && in_array($query['status'] ?? '', ['Assigned','Overdue','TransferredToMe','PreviouslyHandled','In Progress','Resolved','Closed','Reopened'],true)) return false;
    }
    return true;
}
function employee_navigation_active(array $groups, string $page, array $query): ?array
{
    if ($page === 'complaint-centre.php' && !isset($query['view'])) $query['view'] = 'category';
    foreach ($groups as $items) foreach ($items as $item) foreach ($item['links'] as $link) {
        if (employee_navigation_matches($link[0],$page,$query)) return $item;
    }
    // Detail screens belong to the complaint section, not a second navigation item.
    if (in_array($page,['complaint-view.php','officer-complaint.php'],true)) {
        foreach ($groups as $items) foreach ($items as $item) {
            if (in_array($item['key'],['complaints','my-complaints'],true)) return $item;
        }
    }
    return null;
}
