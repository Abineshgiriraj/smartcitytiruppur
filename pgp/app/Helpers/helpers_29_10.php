<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



function isActiveMenu($menu)
{
    $currentRouteName = Route::currentRouteName();
    $activeClass = '';

    if ($currentRouteName === $menu->slug) {
        $activeClass = 'active';
    } elseif (isset($menu->submenu)) {
        if (is_array($menu->slug)) {
            foreach ($menu->slug as $slug) {
                if (str_starts_with($currentRouteName, $slug)) {
                    $activeClass = 'active open';
                    break;
                }
            }
        } else {
            if (str_starts_with($currentRouteName, $menu->slug)) {
                $activeClass = 'active open';
            }
        }
    }

    return $activeClass;
}

if (!function_exists('unique_id')) {
    function unique_id($prefix = '')
    {
        $unique_id = uniqid() . rand(100, 999);

        if ($prefix) {
            $unique_id = $prefix . $unique_id;
        }

        return $unique_id;
    }
}

function get_complaint_type()
{
    $complaint_type = DB::table('complaint_type')->select('unique_id', 'complaint_type')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $complaint_type );

    if ($complaint_type) {

        return $complaint_type;
    } else {
        print_r($complaint_type);
        return 0;
    }
}

function formatDate($dateString, $format = 'Y-m-d')
{
    $date = new DateTime($dateString);
    return $date->format($format);
}
//Acc Year

function acc_year()
{
    $acc_year = '';
    $curr_year = date('Y');

    $today = strtotime(date('d-m-Y'));
    $end_date = strtotime('31-03-' . $curr_year);
    $start_date = strtotime('01-04-' . $curr_year);

    if ($today >= $start_date) {
        $next_year = $curr_year + 1;
        $acc_year = $curr_year . '-' . $next_year;
    } else if ($today <= $end_date) {
        $previous_year = $curr_year - 1;
        $acc_year = $previous_year . '-' . $curr_year;
    }

    return $acc_year;
}
// Uniqui ID Geneartor

function unique_id($prefix = '')
{

    $unique_id = uniqid() . rand(10000, 99999);

    if ($prefix) {
        $unique_id = $prefix . $unique_id;
    }

    return $unique_id;
}

// Convert Original folder Name to Display Name

function disname($name = '')
{
    if ($name) {
        $name = explode('_', $name);
        $name = array_map('ucfirst', $name);
        $name = implode(' ', $name);

        return $name;
    } else {
        return 'Empty Title';
    }
}

function get_screen_type($unique_id = '')
{

    $screen_type = DB::table('user_screen_type')->select('unique_id', 'type_name')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($screen_type) {
        return $screen_type;
    } else {
        print_r($screen_type);
        return 0;
    }
}

function formatToCapitalizedWords($string)
{
    // Replace underscores with spaces
    $formattedString = str_replace('_', ' ', $string);

    // Capitalize the first letter of each word
    $capitalizedString = ucwords(strtolower($formattedString));
    // dd( trim( $capitalizedString ) );

    // Handle cases where the string might already be in a capitalized format
    return trim($capitalizedString);
}

function user_actions($unique_id = '')
{
    $query = DB::table('user_screen_actions')
        ->select('unique_id', 'action_name')
        ->where('is_active', 1)
        ->where('is_delete', 0);

    if ($unique_id) {
        $query->where('unique_id', $unique_id);
    }

    $user_actions = $query->get();

    if ($user_actions->isNotEmpty()) {
        return $user_actions;
    } else {
        return 0;
    }
}

function user_action_list($userActionArray = [], $selected = [])
{
    $allChecked = '';
    $allId = 'all';

    if (is_string($selected) && !empty($selected)) {
        $selectedArray = explode(',', $selected);
    } elseif (is_array($selected)) {
        $selectedArray = $selected;
        // In case $selected is already an array
    }

    $returnStr = '<li>
                    <input type="checkbox" id="dis_check" disabled>
                    <label for="dis_check">No Options</label>
                </li>';

    if (!empty($userActionArray)) {
        $returnStr = '<li>
                        <input type="checkbox" id="' . $allId . '" ' . $allChecked . '>&nbsp;&nbsp;
                        <label for="' . $allId . '">All</label>
                    </li>';

        foreach ($userActionArray as $value) {
            $checked = '';
            $uniqueId = '';
            $actionName = '';

            if (is_object($value)) {
                $uniqueId = $value->unique_id;
                $actionName = $value->action_name;
            } elseif (is_array($value)) {
                $uniqueId = $value['unique_id'];
                $actionName = $value['action_name'];
            }

            if (in_array($uniqueId, $selectedArray)) {
                $checked = ' checked ';
            }

            $returnStr .= '<li  >
                            <input type="checkbox" id="' . htmlspecialchars($uniqueId) . '" ' . $checked . ' name="check_actions" value="' . htmlspecialchars($uniqueId) . '" class="action_check">&nbsp;&nbsp;
                            <label for="' . htmlspecialchars($uniqueId) . '">' . formatToCapitalizedWords(htmlspecialchars($actionName)) . '</label>
                        </li>';
        }
    }

    return $returnStr;
}

function user_main_screen($unique_id = '')
{

    $screen_name = DB::table('main_user_screens')->select('unique_id', 'screen_name')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($screen_name) {
        return $screen_name;
    } else {
        print_r($screen_name);
        return 0;
    }
}

function get_firms($unique_id = '')
{

    $company_name = DB::table('company_creation')->select('unique_id', 'company_name')->where(['is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($company_name) {

        return $company_name;
    } else {
        print_r($company_name);
        return 0;
    }
}

function get_firm_company($unique_id = '')
{

    $company_name = DB::table('firms_registration')->select('unique_id', 'name')->where(['is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($company_name) {

        return $company_name;
    } else {
        print_r($company_name);
        return 0;
    }
}


function get_company($unique_id = '')
{

    $company_name = DB::table('company_creation')->select('unique_id', 'company_name')->where(['is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($company_name) {

        return $company_name;
    } else {
        print_r($company_name);
        return 0;
    }
}

function user_sub_screen($unique_id = '')
{
    $query = DB::table('user_sub_screen')
        ->select('unique_id', 'sub_screen_name')
        ->where('is_active', 1)
        ->where('is_delete', 0);

    if ($unique_id) {
        $query->where('unique_id', $unique_id);
    }

    $userActions = $query->get();
    // Retrieve the results

    if ($userActions->isNotEmpty()) {
        return $userActions;
        // Return the data
    } else {
        return 0;
        // Return 0 if no data found
    }
}

function get_user_type($unique_id = '')
{
    $user_type = DB::table('user_types')->select('unique_id', 'user_type')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($user_type) {
        return $user_type;
    } else {
        print_r($user_type);
        return 0;
    }
}

function user_screen_check($section_unique_id = '')
{

    $screen_sections = DB::table('user_screens')->select('unique_id', 'screen_name', 'action')->where([['main_screen', '=', $section_unique_id], ['is_delete', '=', 0]])->orderBy('id', 'ASC')->get();
    // dd( $screen_sections );

    // $main_screens = $pdo->select( $table_details, $where, '', '', $order_by );

    if ($screen_sections) {
        // print_r( $screen_section );
        return $screen_sections;
    } else {
        print_r($screen_sections);
        return 0;
    }
}

function user_actions_check($section_unique_id = '')
{
    $user_actions = DB::table('user_screen_actions')->select('unique_id', 'action_name')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('s_no', 'ASC')->get();

    // $main_screens = $pdo->select( $table_details, $where, '', '', $order_by );

    if ($user_actions) {
        // print_r( $screen_section );
        return $user_actions;
    } else {
        print_r($user_actions);
        return 0;
    }
}

//main screen check

function section_name_check($main_screen_id = '')
{
    // dd( $main_screen_id );

    $screen_sections = DB::table('main_user_screens')->select('unique_id', 'screen_name')->where([['unique_id', '=', $main_screen_id], ['is_delete', '=', 0]])->orderBy('id', 'ASC')->get();
    // $screen_sections = DB::table( 'user_screen_main' )->select( 'unique_id', 'screen_main_name' )->where( 'unique_id', '=', $main_screen_unique_id )->get();
    // dd( $screen_sections );

    // $main_screens = $pdo->select( $table_details, $where, '', '', $order_by );

    if ($screen_sections) {
        // print_r( $screen_sections );
        return $screen_sections;
    } else {
        print_r($screen_sections);
        return 0;
    }
}

// function section() {
//  global pdo;

//     $table = 'main_screen_table';

//     $columns = [
//         'unique_id',
//         'main_screen_name'
// ];

//     $tabele_details = [
//         $table,
//         $columns
// ];

//     $action_obj = $pdo->select( $table_details, $where );

// }

function user_permission_ui($main_screen_id = '', $user_type = '', $department_name = '')
{

    $total_ui = '';

    if ($main_screen_id) {
        // dd( $main_screen_id );
        $screen_sections = section_name_check($main_screen_id);

        // Section Tab UI starts Here

        $total_ui .= '<ul class="nav nav-pills navtab-bg nav-justified">';

        $show = 1;
        $show1 = 1;

        foreach ($screen_sections as $section_value) {

            // dd( $section_value );
            $active = '';

            if ($show++ == 1) {
                $active = ' active ';
            }

            $section_name = $section_value->screen_name;
            $section_unique_id = $section_value->unique_id;

            $total_ui .= '<li class="nav-item">
                            <a href="#sec' . $section_unique_id . '" data-bs-toggle="tab" aria-expanded="true" class="nav-link ' . $active . '">
                                ' . $section_name . '
                            </a>
                        </li>';
        }

        $total_ui .= '</ul>';

        // Section Tab UI Ends Here

        $total_ui .= '<div class="tab-content">';
        // Section Tab Content UI Starts Here
        foreach ($screen_sections as $section_value) {

            $active = '';

            if ($show1++ == 1) {
                $active = ' show active ';
            }

            $section_name = $section_value->screen_name;
            $section_unique_id = $section_value->unique_id;

            // Tab DIV Start
            $total_ui .= '<div class="tab-pane ' . $active . '" id="sec' . $section_unique_id . '">';

            // Get Section Based Screens
            $user_screens = user_screen_check($section_unique_id);
            $user_actions = user_actions_check();

            // UI Table Start
            $total_ui .= '<div class="row">
                                        <div class="col-12">';

            $total_ui .= '<table class="user_permission_datatable table dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>';

            $total_ui .= '<th>#</th>';
            $total_ui .= '<th>Screen</th>';
            $total_ui .= '<th>';
            // $total_ui           .= '<span class="toggle-button toggle-button--tuuli">
            //                             <input id="all'.$section_unique_id.'" type="checkbox">
            //                             <label for="all'.$section_unique_id.'"></label>
            //                             <div class="toggle-button__icon"></div>
            //                         </span>';
            $total_ui .= '<button onclick="check_all(\'all' . $section_unique_id . '\',this)" type="button" data-id = "all' . $section_unique_id . '" data-check="checked" class="btn btn-sm btn-soft-info btn-rounded waves-effect waves-light" value="unchecked">All</button>';
            $total_ui .= '</th>';

            foreach ($user_actions as $action_value) {

                $action_name = $action_value->action_name;

                $total_ui .= '<th><button onclick="check_all(\'' . $action_name . $section_unique_id . '\',this)" data-check="unchecked" type="button" data-id = "' . $action_name . $section_unique_id . '" class="btn btn-sm btn-soft-info btn-rounded waves-effect waves-light" value="unchecked">' . $action_name . '</button></th>';
            }

            $total_ui .= '    </tr>
                                    </thead>';

            $total_ui .= '<tbody>';

            $s_no = 1;

            foreach ($user_screens as $screen_value) {

                $all_checked = '';

                $user_per_arr = '';

                $screen_name = $screen_value->screen_name;
                $screen_unique_id = $screen_value->unique_id;
                $screen_actions = explode(',', $screen_value->action);
                // $screen_actions     = $screen_value->actions;

                // if ( ( $user_type ) && ( $department_name ) ) {
                //     $screen_perm = get_permissions( $user_type, '', '', '', $screen_unique_id, $department_name );
                //     // print_r( $screen_perm );
                //     if ( empty( $screen_perm ) ) {
                //         $screen_perm = [];
                //     } else {
                //         $screen_perm = explode( ',', $screen_perm[ 0 ][ 'action_unique_id' ] );

                //         if ( count( $screen_actions ) == count( $screen_perm ) ) {
                //             $all_checked = ' checked ';
                //         }
                //     }

                // }

                $total_ui .= '<tr>';
                $total_ui .= '<td>' . $s_no . '</td>';
                $total_ui .= '<td>' . $screen_name . '</td>';
                $total_ui .= '<td>
                                        <span class="toggle-button toggle-button--tuuli">
                                            <input class = "all' . $section_unique_id . '" onclick="check_all(\'screen' . $screen_unique_id . '\',this)" name="checkall" id="checkall' . $screen_unique_id . '" value="' . $screen_unique_id . '" type="checkbox" ' . $all_checked . '>
                                            <label for="all' . $screen_unique_id . '"></label>
                                            <div class="toggle-button__icon"></div>
                                        </span>
                                    </td>';

                foreach ($user_actions as $action_value) {

                    $checked = '';
                    $action_unique_id = $action_value->unique_id;
                    $total_ui .= '<td>';

                    // if ( ( $user_type ) && ( $department_name ) ) {
                    //     if ( in_array( $action_unique_id, $screen_perm ) ) {
                    //         $checked            = ' checked ';
                    //     }
                    // }

                    if (in_array($action_unique_id, $screen_actions)) {

                        $total_ui .= '<span class="toggle-button toggle-button--tuuli">

                                            <input onclick="check_me(\'' . $screen_unique_id . '\',this)" class="all-checkbox ' . $action_name . $section_unique_id . ' all' . $section_unique_id . ' screen' . $screen_unique_id . ' allcheck-' . $screen_unique_id . '" data-main="' . $main_screen_id . '" data-section="' . $section_unique_id . '" data-screen="' . $screen_unique_id . '" data-action="' . $action_unique_id . '" id="' . $action_unique_id . '" ' . $checked . ' name="checkboxvalue[]" value="' . $action_unique_id . '" type="checkbox">

                                            <label for="' . $screen_unique_id . '-' . $action_unique_id . '"></label>
                                            <div class="toggle-button__icon"></div>
                                        </span>';
                    } else {
                        $total_ui .= '<span class="table_empty_dot"></span>';
                    }

                    $total_ui .= '</td>';
                }

                $total_ui .= '</tr>';

                $s_no++;
                // return $screen_unique_id;

            }

            $total_ui .= '</tbody>';
            $total_ui .= '</table>';
            // UI Table End
            $total_ui .= '    </div>
                                    </div>';

            // Tab DIV End
            $total_ui .= '</div>';
        }
        $total_ui .= '</div>';

        // Section Tab Content UI Ends Here
    }

    return $total_ui;
}

//  function btn_update( $route_name, $unique_id, $folder_name = '', $prefix = '', $suffix = '' )
// {
//     $editUrl = route( $route_name, [ 'unique_id' => $unique_id ] );

//     // Generate the final string with the constructed URL
//     // class = 'btn btn-primary btn-action mr-1'
//     $final_str = '<a href="' . $editUrl . '"><i class="bx bxs-edit"></i></a>';

//     return $final_str;
// }

if (!function_exists('session')) {
    /**
     * Get / set the specified session value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string|null  $key
     * @param  mixed  $default
     * @return mixed|\Illuminate\Session\Store|\Illuminate\Session\SessionManager
     */

    function session($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('session');
        }

        if (is_array($key)) {
            return app('session')->put($key);
        }

        return app('session')->get($key, $default);
    }
}

function btn_update($route_name, $unique_id, $folder_name = '', $prefix = '', $suffix = '')
{
    // Modify the URL structure to include the unique ID before the edit segment
    $editUrl = route($route_name, ['unique_id' => $unique_id]);

    // Generate the final string with the constructed URL
    // class = 'btn btn-primary btn-action mr-1'
    $final_str = '<a href="' . $editUrl . '"><i class="bx bxs-edit"></i></a>';

    return $final_str;
}

function btn_delete($route_name, $unique_id, $folder_name = '', $prefix = '', $suffix = '')
{
    $deleteUrl = route($route_name, ['unique_id' => $unique_id]);

    // Generate the final string with the constructed URL
    // class = 'btn btn-primary btn-action mr-1'
    $final_str = '<a href="' . $deleteUrl . '"><i class="bx bx-trash"></i></a>';

    return $final_str;
}

function get_zone($unique_id = '')
{

    $zone_names = DB::table('zones')->select('unique_id', 'zone')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('id', 'ASC')->get();

    // print_r( $zone_names );
    // die();
    if ($zone_names) {
        return $zone_names;
    } else {
        print_r($zone_names);
        return 0;
    }
}

function taluk_names($unique_id = '')
{

    $taluk_names = DB::table('taluk')->select('unique_id', 'taluk_name')->where(['is_active' => 1, 'is_delete' => 0])->where('unique_id', '<>', '641828393e9b989808')->where('unique_id', '<>', '641827f68916470107')->where('unique_id', '<>', '641827b4f11e514254')->where('unique_id', '<>', '6418277e41dbc55270')->where('unique_id', '<>', '6418280fc395690980')->orderBy('id', 'ASC')->get();

    if ($taluk_names) {
        return $taluk_names;
    } else {
        print_r($taluk_names);
        return 0;
    }
}

function get_designation($unique_id = '')
{

    $designation = DB::table('employee_designation')->select('unique_id', 'designation')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($designation) {

        return $designation;
    } else {
        print_r($designation);
        return 0;
    }
}

function academic_year($unique_id = '')
{
    $academic_year = DB::table('acc_years')->select('unique_id', 'acc_year')->where('is_delete', 0,)->where( 'is_active', 1)->orderBy('id', 'DESC')->get();

    if ($academic_year) {
        return $academic_year;
    } else {
        print_r($academic_year);
        return 0;
    }
}


function accountic_year($unique_id = '')
{
    $academic_year = DB::table('acc_years')->select('unique_id', 'acc_year')->where('is_delete', 0,)->where( 'is_active', 1)->orderBy('id', 'DESC')->first();

    if ($academic_year) {
        return $academic_year;
    } else {
        print_r($academic_year);
        return 0;
    }
}

function get_firm($unique_id = '')
{

    $firms = DB::table('firms_registration')->select('unique_id', 'firms')->where(['is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($firms) {

        return $firms;
    } else {
        print_r($firms);
        return 0;
    }
}

function get_district($unique_id = '')
{

    $district = DB::table('district')->select('unique_id', 'district')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($district) {
        return $district;
    } else {
        print_r($district);
        return 0;
    }
}

function get_school_name($unique_id = '')
{

    $school = DB::table('schools')->select('unique_id', 'school_name')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($school) {
        return $school;
    } else {
        print_r($school);
        return 0;
    }
}

function get_punch_category()
{
    $category = DB::table('biometric_attendance')->select('verify_mode')->where([])->orderBy('id', 'ASC')->get();

    if ($category) {
        return $category;
    } else {
        print_r($category);
        return 0;
    }
}

function user_type()
{
    $user_type = DB::table('user_types')->select('unique_id', 'user_type')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    if ($user_type) {
        return $user_type;
    } else {
        print($user_type);
        return 0;
    }
}

function main_screen()
{
    $main_screen = DB::table('main_user_screens')->select('unique_id', 'screen_name')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    if ($main_screen) {
        $main_screen;
    } else {
        print_r($main_screen);
        return 0;
    }
}

function get_cargo()
{
    $cargo = DB::table('cargo_creation')->select('unique_id', 'cargo')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $cargo );

    if ($cargo) {

        return $cargo;
    } else {
        print_r($cargo);
        return 0;
    }
}

function get_access_gate()
{
    $access_gate = DB::table('access_gate')->select('unique_id', 'access_gate')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $access_gate );

    if ($access_gate) {

        return $access_gate;
    } else {
        print_r($access_gate);
        return 0;
    }
}

function get_v_gate()
{
    $access_gate = DB::table('access_gate')->select('unique_id', 'access_gate')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $access_gate );

    if ($access_gate) {

        return $access_gate;
    } else {
        print_r($access_gate);
        return 0;
    }
}

function get_vehicle_category()
{
    $vehicle_category = DB::table('vehicle_creation')->select('unique_id', 'vehicle_type')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $vehicle_type );

    if ($vehicle_category) {

        return $vehicle_category;
    } else {
        print_r($vehicle_category);
        return 0;
    }
}


function get_department()
{
    $department = DB::table('department_creation')->select('unique_id', 'department')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $department );

    if ($department) {

        return $department;
    } else {
        print_r($department);
        return 0;
    }
}

// function formatDate( $dateString, $format = 'Y-m-d' )
// {
//     $date = new DateTime( $dateString );
//     return $date->format( $format );
// }
//Acc Year
if (!function_exists('formatCurrencyINR')) {
    function formatCurrencyINR($amount)
    {
        return '₹' . number_format($amount, 2, '.', ',');
    }
}
// Uniqui ID Geneartor

function generateScreenUniqueId($prefix = 'scr')
{

    $unique_id = uniqid() . rand(10000, 99999);

    // Assuming you have a function to check if the ID already exists
    // while ( idExists( $uniqueId ) ) {
    if ($prefix) {
        $uniqueId = $prefix . $unique_id;
    }
    // $uniqueId = $prefix . uniqid( '', true );
    // }

    return $uniqueId;
}
// firms type

function get_firm_reg_type()
{
    $firm_registration = DB::table('firms_registration')->select('unique_id', 'firms')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $complaint_type );

    if ($firm_registration) {

        return $firm_registration;
    } else {
        print_r($firm_registration);
        return 0;
    }
}
// firms type

function get_agent_type()
{
    $firm_agent_type = DB::table('company_creation')->select('unique_id', 'company_name')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $complaint_type );

    if ($firm_agent_type) {

        return $firm_agent_type;
    } else {
        print_r($firm_agent_type);
        return 0;
    }
}

// vech type

function get_vech_type()
{
    $vech_type = DB::table('vehicle_creation')->select('unique_id', 'vehicle_type')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $complaint_type );

    if ($vech_type) {

        return $vech_type;
    } else {
        print_r($vech_type);
        return 0;
    }
}




function get_person_type()
{
    $person_type = DB::table('employee_designation')->select('unique_id', 'designation')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $person_type );

    if ($person_type) {

        return $person_type;
    } else {
        print_r($person_type);
        return 0;
    }
}

function get_vehicle_type()
{
    $vehicle_type = DB::table('cargo_creation')->select('unique_id', 'cargo')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $vehicle_type );

    if ($vehicle_type) {

        return $vehicle_type;
    } else {
        print_r($vehicle_type);
        return 0;
    }
}


function get_centers($unique_id = '')
{

    $company_name = DB::table('centers')->select('unique_id', 'center_name')->where(['is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($company_name) {

        return $company_name;
    } else {
        print_r($company_name);
        return 0;
    }
}


function access_area_gate()
{
    $access_gate = DB::table('port_area_access')->select('unique_id', 'area_name')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $access_gate );

    if ($access_gate) {

        return $access_gate;
    } else {
        print_r($access_gate);
        return 0;
    }
}