<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\ProductCreation;



function isActiveMenu($menu)
{
    $currentRouteName = Route::currentRouteName();
    $activeClass = '';

    if ($currentRouteName === $menu) {
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


function millsectoDatetime($input)
{
    // dd($input);
    $timestampMilliseconds = 1737465089000;

    // Convert milliseconds to seconds
    $timestampSeconds = $input / 1000;

    // Create a DateTime object
    $dateTime = new DateTime();
    $dateTime->setTimestamp($timestampSeconds);

    // Format the date-time
    $formattedDateTime = $dateTime->format('Y-m-d H:i:s');
    return $formattedDateTime;
}



function dateFormat($data)
{

    $dateTime = new DateTime($data);

    // Format the date-time
    $formattedDateTime = $dateTime->format('Y-m-d H:i:s');
    return $formattedDateTime;
}


function get_gate_details()
{
    $complaint_type = DB::table('area_gate_master')->select('unique_id', 'gate_name')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $complaint_type );

    if ($complaint_type) {

        return $complaint_type;
    } else {
        print_r($complaint_type);
        return 0;
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

function get_agent_name()
{
    $get_agent_name = DB::table('firms_registration')->select('unique_id', 'name')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $complaint_type );

    if ($get_agent_name) {

        return $get_agent_name;
    } else {
        print_r($get_agent_name);
        return 0;
    }
}


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

    $screen_name = DB::table('main_user_screens')->select('unique_id', 'screen_name', 'icon')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('order_no', 'ASC')->get();

    if ($screen_name) {
        return $screen_name;
    } else {
        print_r($screen_name);
        return 0;
    }
}

function get_firms($unique_id = '')
{

    $company_name = DB::table('company_creation')->select('unique_id', 'company_name')
        ->where(['is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($company_name) {

        return $company_name;
    } else {
        print_r($company_name);
        return 0;
    }
}

function get_firms_type($unique_id = '')
{

    $company_name = DB::table('company_creation')->select('unique_id', 'company_name')
        ->where('unique_id', '!=', '677231ae47a0d26001')
        ->where(['is_delete' => 0])->orderBy('id', 'ASC')->get();

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
function get_zone($unique_id = '')
{

    $zone_name = DB::table('zone')->select('unique_id', 'zone')->where(['is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($zone_name) {

        return $zone_name;
    } else {
        print_r($zone_name);
        return 0;
    }
}

function get_ward($unique_id = '')
{

    $ward_name = DB::table('ward')->select('unique_id', 'ward')->where(['is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($ward_name) {

        return $ward_name;
    } else {
        print_r($ward_name);
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
    // dd($screen_sections);

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

    $designation = DB::table('designation')->select('unique_id', 'designation')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($designation) {

        return $designation;
    } else {
        print_r($designation);
        return 0;
    }
}

function academic_year($unique_id = '')
{
    $academic_year = DB::table('acc_years')->select('unique_id', 'acc_year')->where('is_delete', 0,)->where('is_active', 1)->orderBy('id', 'DESC')->get();

    if ($academic_year) {
        return $academic_year;
    } else {
        print_r($academic_year);
        return 0;
    }
}

function accountic_year($unique_id = '')
{
    $academic_year = DB::table('acc_years')->select('unique_id', 'acc_year')->where('is_delete', 0,)->where('is_active', 1)->orderBy('id', 'DESC')->first();

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

function get_vehicle_doc()
{
    $vehicle_category = DB::table('document_master')->select('unique_id', 'type_of_proof')->where(['category_type' => '672db5499090640321', 'is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $vehicle_type );

    if ($vehicle_category) {

        return $vehicle_category;
    } else {
        print_r($vehicle_category);
        return 0;
    }
}


function get_document()
{
    $get_document_name = DB::table('document_master')->select('unique_id', 'type_of_proof')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $complaint_type );

    if ($get_document_name) {

        return $get_document_name;
    } else {
        print_r($get_document_name);
        return 0;
    }
}

function get_equipment_doc()
{
    $vehicle_category = DB::table('document_master')->select('unique_id', 'type_of_proof')->where(['category_type' => '672db69b89da339500', 'is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

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
    $department = DB::table('department')->select('unique_id', 'department')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

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
    $person_type = DB::table('person_type')->select('unique_id', 'person_type')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $person_type );

    if ($person_type) {

        return $person_type;
    } else {
        print_r($person_type);
        return 0;
    }
}

function get_person_type_permit()
{
    $person_type = DB::table('person_type')
        ->select('unique_id', 'person_type')
        ->where('is_delete', 0)
        ->where('is_active', 1)
        ->where('unique_id', '!=', '66fe76a3c9ce479492')
        ->orderBy('id', 'ASC')
        ->get();

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

// new change
function access_area_zone()
{
    $access_zone = DB::table('access_zone')->select('unique_id', 'zone_type')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $access_gate );

    if ($access_zone) {

        return $access_zone;
    } else {
        print_r($access_zone);
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

function gate_detail()
{
    $access_gate = DB::table('area_gate_master')->select('unique_id', 'gate_name')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    if ($access_gate) {

        return $access_gate;
    } else {
        print_r($access_gate);
        return 0;
    }
}


// function custom_public_path( $path = '' ) {
//  return '/documents/' . ( $path ? DIRECTORY_SEPARATOR . $path : $path );
// }
function custom_public_path_test($path = '')
{
    $basePath = '/var/www/html/documents'; // Base directory is '/var/www/html/documents'.
    return $basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

function custom_public_path($path = '')
{
    $basePath = 'public/uploads';
    return $basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}





// function menu_permission($user_type_id = "") {
//     $return_arr = [
//         "main_screen_id"  => "",
//         "screen_unique_id" => ""
//     ];

//     if ($user_type_id) {
//         // Fetch the screen permissions for the user type
//         $screen_sections = DB::table('user_screen_permission')
//             ->where('user_type', $user_type_id)
//             ->select(DB::raw('group_concat(screen_unique_id) AS screen_unique_id'))
//             ->groupBy('user_type')
//             ->get();
//         // Check if data was fetched
//         if ($screen_sections->isNotEmpty()) {

//             // Assuming you want to split the screen_unique_id into an array of individual IDs
//             $screen_ids = explode(',', $screen_sections[0]->screen_unique_id);
//             return $screen_ids;  // Return the array of screen_unique_ids
//         } else {
//             return []; // Return an empty array if no data is found
//         }
//     }

//     return $return_arr; // Return default value if user_type_id is not passed
// }

// function menu_permission_main($user_type_id = "") {
//     $return_arr = [
//         "main_screen_id"  => "",
//         "screen_unique_id" => ""
//     ];

//     if ($user_type_id) {
//         // Fetch the main screen permissions for the user type
//         $main_screen_unique_id = DB::table('user_screen_permission')
//             ->where('user_type', $user_type_id)
//             ->select(DB::raw('group_concat(main_screen_id) AS main_screen_id'))
//             ->groupBy('user_type')
//             ->get();

//         // Check if data was fetched
//         if ($main_screen_unique_id->isNotEmpty()) {
//             // Assuming you want to split the main_screen_id into an array of individual IDs
//             $main_screen_ids = explode(',', $main_screen_unique_id[0]->main_screen_id);
//             return $main_screen_ids;  // Return the array of main_screen_ids
//         } else {
//             return []; // Return an empty array if no data is found
//         }
//     }

//     return $return_arr; // Return default value if user_type_id is not passed
// }

function menu_permission($user_type_id = "")
{
    $return_arr = [
        "main_screen_id" => "",
        "screen_unique_id" => ""
    ];

    if ($user_type_id) {

        $screen_sections = DB::table('user_screen_permission')
            ->where('user_type', $user_type_id)
            ->select('screen_unique_id')
            ->groupBy('screen_unique_id')
            ->get();

        // $screen_sections = DB::table('user_screen_permission')
        // ->where('user_type', $user_type_id)
        // // ->select(DB::raw('group_concat(screen_unique_id ORDER BY id ASC) AS screen_unique_id'))
        // ->groupBy('screen_unique_id')  // Add GROUP BY clause
        // ->get();
        // dd($screen_sections);

        if ($screen_sections) {

            return $screen_sections;
        } else {
            print_r($screen_sections);
            return 0;
        }
    }
    return $return_arr;
}

function menu_permission_main($user_type_id = "")
{
    $return_arr = [
        "main_screen_id" => "",
        "screen_unique_id" => ""
    ];

    if ($user_type_id) {

        $main_screen_unique_id = DB::table('user_screen_permission')
            ->where('user_type', $user_type_id)
            ->select('main_screen_id')
            ->groupBy('main_screen_id')
            ->get();

        if ($main_screen_unique_id) {
            return $main_screen_unique_id;
        } else {
            print_r($main_screen_unique_id);
            return 0;
        }
    }
    return $return_arr;
}




// old code<?php

function user_screen_names($section_unique_id)
{
    $screen_sections = DB::table('user_screens')->select("unique_id", "screen_name", "folder_name", "icon", "main_screen", "action")->where("main_screen", '=', $section_unique_id)->where("is_delete", '=', 0)->where("is_active", '=', 1)->orderBy('order_no', 'ASC')->get();

    // print_r($screen_sections);die();

    // $main_screens = $pdo->select($table_details, $where,'','',$order_by);
    // dd($screen_sections);
    if ($screen_sections) {
        // print_r($screen_section);
        return $screen_sections;
    } else {
        print_r($screen_sections);
        return 0;
    }
}





function user_permission_ui_update($action_unique = "", $main_screen_id = "", $user_type = "", $department_name = "", $screen_unique_id_count = "", $action_unique_id_count = "", $action_unique_ids = "", $screen_unique_ids = "")
{

    // dd($screen_unique_ids);
    $total_ui = '';

    if ($main_screen_id) {
        // dd($main_screen_id);
        $screen_sections = section_name_check($main_screen_id);

        // Section Tab UI starts Here

        $total_ui .= '<ul class="nav nav-pills navtab-bg nav-justified">';

        $show = 1;
        $show1 = 1;

        foreach ($screen_sections as $section_value) {

            // dd($section_value);
            $active = "";

            if ($show++ == 1) {
                $active = " active ";
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

            $active = "";

            if ($show1++ == 1) {
                $active = " show active ";
            }

            $section_name = $section_value->screen_name;
            $section_unique_id = $section_value->unique_id;


            // Tab DIV Start
            $total_ui .= '<div class="tab-pane ' . $active . '" id="sec' . $section_unique_id . '">';

            // Get Section Based Screens
            $user_screens = user_screen_check_up($section_unique_id);
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
            $total_ui .= '<button onclick="check_all(\'all' . $section_unique_id . '\',this),take_value(\'screen' . $section_unique_id . '\',this)" type="button" data-id = "all' . $section_unique_id . '" data-check="checked" class="btn btn-sm btn-soft-info btn-rounded waves-effect waves-light" value="unchecked">All</button>';
            $total_ui .= '</th>';

            foreach ($user_actions as $action_value) {

                $action_name = $action_value->action_name;

                $total_ui .= '<th><button onclick="check_all(\'' . $action_name . $section_unique_id . '\',this),take_value(\'screen' . $section_unique_id . '\',this)" data-check="unchecked" type="button" data-id = "' . $action_name . $section_unique_id . '" class="btn btn-sm btn-soft-info btn-rounded waves-effect waves-light" value="unchecked">' . $action_name . '</button></th>';
            }

            $total_ui .= '    </tr>
                                    </thead>';

            $total_ui .= '<tbody>';

            $s_no = 1;

            foreach ($user_screens as $screen_value) {

                $all_checked = "";

                $user_per_arr = "";


                $screen_name = $screen_value->screen_name;
                $screen_unique_id = $screen_value->unique_id;
                // dd($screen_unique_id);
                // dd($department_name);
                $screen_actions = explode(",", $screen_value->action);

                if (($user_type)) {
                    //    dd($screen_unique_id);

                    $screen_perm = get_permissions1($user_type, $screen_unique_id, "", "", "",);

                    if (empty($screen_perm)) {
                        $screen_perm = [];
                    } else {

                        foreach ($screen_perm as $screens) {
                            $screen_perm = explode(",", $screens->action_unique_id);
                            // dd($screens->action_unique_id);
                            if (count($screen_actions) == count($screen_perm)) {

                                $all_checked = "checked";
                            }
                        }
                    }
                    // if($screen_unique_id)

                    $total_ui .= '<tr>';
                    $total_ui .= '<td>' . $s_no . '</td>';
                    $total_ui .= '<td>' . $screen_name . '</td>';

                    $total_ui .= '<td><span class="toggle-button toggle-button--tuuli"><input class = "checkall' . $section_unique_id . '" onclick="check_all(\'screen' . $screen_unique_id . '\',this),take_value(\'screen' . $screen_unique_id . '\',this)"  name="checkall" id="checkall' . $screen_unique_id . '" value="' . $screen_unique_id . '" type="checkbox" ' . $all_checked . '  ><label for="all' . $screen_unique_id . '"></label><div class="toggle-button__icon">
        <input type="hidden" name="screen_id[]" id="screen_id[]" value="' . $screen_unique_id . '"></div></span></td>';
                    // value='.$screen_unique_id.'if($screen_unique_id){ echo "<input type="text" name="screen_id" id="screen_id" value='.$screen_unique_id.' >"}else{ echo "<input type="text" name="screen_id" id="screen_id" >"}
                }
                foreach ($user_actions as $action_value) {

                    $checked = "";
                    $action_unique_id = $action_value->unique_id;
                    $total_ui .= '<td>';

                    if (($user_type) && ($department_name)) {
                        if (in_array($action_unique_id, $screen_perm)) {
                            $checked = " checked ";
                        }
                    }

                    if (in_array($action_unique_id, $screen_actions)) {


                        $total_ui .= '<span class="toggle-button toggle-button--tuuli">

                                            <input onclick="check_me(\'' . $screen_unique_id . '\',this)" class="all-checkbox ' . $action_name . $section_unique_id . ' all' . $section_unique_id . ' screen' . $screen_unique_id . ' allcheck-' . $screen_unique_id . '" data-main="' . $main_screen_id . '" data-section="' . $section_unique_id . '" data-screen="' . $screen_unique_id . '" data-action="' . $action_unique_id . '" id="' . $action_unique_id . '" ' . $checked . ' name="checkboxvalue[]" value="' . $screen_unique_id . '-' . $action_unique_id . '" type="checkbox" ' . $checked . ' >




                                            <label for="' . $screen_unique_id . "-" . $action_unique_id . '"></label>
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


function user_screen_check_up($section_unique_id = "")
{

    $screen_sections = DB::table('user_screens')
        ->select("unique_id", "screen_name", "main_screen", "action")
        ->where([["main_screen", '=', $section_unique_id], ["is_delete", "=", 0]])->get();

    if ($screen_sections) {
        return $screen_sections;
    } else {
        print_r($screen_sections);
        return 0;
    }
}


function get_permissions1($user_type = "", $screen_unique_id = "", $main_screen = null, $screen_section = null, $screens = null, $screen_actions = null)
{
    if ($user_type) {
        $all_permissions = [];
        $main_permissions = [];
        $section_permissions = [];
        $screen_permissions = [];
        $action_permissions = [];

        $result_values = DB::table('user_screen_permission')
            ->select(DB::raw('group_concat(action_unique_id) as action_unique_id'))
            ->where([['is_delete', '=', 0], ['screen_unique_id', '=', $screen_unique_id], ['user_type', '=', $user_type]])
            ->get();

        if ($screen_actions) {

            $select = (DB::raw('group_concat(action_unique_id) as action_unique_id'));

            $group_by = "screen_unique_id";

            $where['screen_unique_id'] = $screen_actions;
        }

        return $result_values;
    }
}


function get_permissions($userType = "", $mainScreen = null, $screenSection = null, $screens = null, $screenActions = null)
{
    if ($userType) {
        $permissionTable = 'user_screen_permission';

        // Initialize columns, where conditions, and group by
        $columns = [
            'user_type',
            'main_screen_id',
            // 'section_unique_id',
            'screen_unique_id',
            DB::raw('GROUP_CONCAT(action_unique_id) AS action_unique_id')
        ];

        $where = [
            'is_delete' => 0,
            'is_active' => 1,
            'user_type' => $userType
        ];

        $groupBy = 'screen_unique_id';

        // Adjust columns and grouping based on parameters
        if ($mainScreen) {
            $columns = [
                DB::raw('GROUP_CONCAT(main_screen_id) AS main_screen_id')
            ];
            $groupBy = null;
        }

        // Uncomment and adjust for screenSection if needed
        // if ($screenSection) {
        //     $columns = [
        //         DB::raw('GROUP_CONCAT(section_unique_id) AS section_unique_id')
        //     ];
        //     $groupBy = null;
        // }

        if ($screens) {
            $columns = [
                DB::raw('GROUP_CONCAT(screen_unique_id) AS screen_unique_id')
            ];
            $groupBy = null;
        }

        if ($screenActions) {
            $columns = [
                DB::raw('GROUP_CONCAT(action_unique_id) AS action_unique_id')
            ];
            $groupBy = 'screen_unique_id';
            $where['screen_unique_id'] = $screenActions;
        }

        // Build the query
        $query = DB::table($permissionTable)
            ->select($columns)
            ->where($where);

        // Apply group by if needed
        if ($groupBy) {
            $query->groupBy($groupBy);
        }

        // Execute the query
        $resultValues = $query->get();

        // Return result based on status
        if ($resultValues->isNotEmpty()) {
            return $resultValues;
        } else {
            return null;
        }
    }
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

function get_reason_type()
{
    $reason_type = DB::table('reason_type')->select('unique_id', 'reason_type')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $complaint_type );

    if ($reason_type) {

        return $reason_type;
    } else {
        print_r($reason_type);
        return 0;
    }
}


function get_hepcard_type()
{
    $hepcard_type = DB::table('hepcard_type')->select('unique_id', 'hepcard_type')->where(['is_delete' => 0, 'is_active' => 1])->orderBy('id', 'ASC')->get();

    // print( $complaint_type );

    if ($hepcard_type) {

        return $hepcard_type;
    } else {
        print_r($hepcard_type);
        return 0;
    }
}


function get_pass_type()
{
    $get_pass_type = DB::table('pass_entry_sublist')->select('unique_id', 'pass_type')->where(['is_delete' => 0, 'is_active' => 1])
        ->orderBy('id', 'ASC')->get();

    // print( $complaint_type );

    if ($get_pass_type) {

        return $get_pass_type;
    } else {
        print_r($get_pass_type);
        return 0;
    }
}

function generateScreenUniqueId1()
{
    // Get the current year and month
    $yearMonth = date('Ym');
    $prefix = "GP{$yearMonth}";

    // Generate a 5-digit random number between 1 and 99999, padded with leading zeros if necessary
    $randomNumber = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

    // Combine the prefix with the 5-digit random number
    $uniqueId = $prefix . $randomNumber;

    return $uniqueId;
}

function get_unit_options($selected = null)
{
    $units = DB::table('unit')
        ->where('is_active', 1)
        ->where('is_delete', 0)
        ->orderBy('unit_name')
        ->get();

    $options = '<option value="">Select Unit</option>';

    foreach ($units as $unit) {
        $isSelected = ($selected == $unit->unique_id) ? 'selected' : '';
        $options .= '<option value="' . $unit->unique_id . '" ' . $isSelected . '>' . $unit->unit_name . '</option>';
    }

    return $options;
}

function get_product_group_options($selected = null)
{
    $groups = DB::table('group_creation')
        ->where('is_active', 1)
        ->where('is_delete', 0)
        ->orderBy('group_name')
        ->get();

    $options = '<option value="">Select Product Group</option>';

    foreach ($groups as $group) {
        $isSelected = ($selected == $group->unique_id) ? 'selected' : '';
        $options .= '<option value="' . $group->unique_id . '" ' . $isSelected . '>' . $group->group_name . '</option>';
    }

    return $options;
}

function get_product_category_options($selected = null, $group_id = null)
{
    $query = DB::table('category')
        ->where('is_active', 1)
        ->where('is_delete', 0);

    if (!is_null($group_id)) {
        $query->where('group_id', $group_id);
    }

    $categories = $query->orderBy('category_name')->get();

    $options = '<option value="">Select Product Category</option>';

    foreach ($categories as $category) {
        $isSelected = ($selected == $category->unique_id) ? 'selected' : '';
        $options .= '<option value="' . $category->unique_id . '" ' . $isSelected . '>' . $category->category_name . '</option>';
    }

    return $options;
}

function get_spare_category_options($selected = null, $group_id = null)
{
    $query = DB::table('spare_category')
        ->where('is_active', 1)
        ->where('is_delete', 0);

    if (!is_null($group_id)) {
        $query->where('group_id', $group_id);
    }

    $categories = $query->orderBy('category_name')->get();

    $options = '<option value="">Select Spare Category</option>';

    foreach ($categories as $category) {
        $isSelected = ($selected == $category->unique_id) ? 'selected' : '';
        $options .= '<option value="' . $category->unique_id . '" ' . $isSelected . '>' . $category->category_name . '</option>';
    }

    return $options;
}


function get_product_id($selected = [])
{
    $products = ProductCreation::all();
    $options = '<option value="">Select Product ID</option>';

    foreach ($products as $product) {
        $selectedAttr = in_array($product->unique_id, (array) $selected) ? 'selected' : '';
        $options .= '<option value="' . $product->unique_id . '" ' . $selectedAttr . '>' . $product->product_id . '</option>';
    }

    return $options;
}

function get_group_id($selected = null)
{
    $groups = DB::table('group_creation')
        ->where('is_active', 1)
        ->where('is_delete', 0)
        ->orderBy('group_name')
        ->get();

    $options = '<option value="">Select Group ID</option>';

    foreach ($groups as $group) {
        $isSelected = ($selected == $group->unique_id) ? 'selected' : '';
        $options .= '<option value="' . $group->unique_id . '" ' . $isSelected . '>' . $group->group_name . '</option>';
    }

    return $options;
}

// function get_spare_group_id($selected = null)
// {
//     $groups = DB::table('spare_group')
//         ->where('is_active', 1)
//         ->where('is_delete', 0)
//         ->orderBy('group_name')
//         ->get();

//     $options = '<option value="">Select Group Name</option>';

//     foreach ($groups as $group) {
//         $isSelected = ($selected == $group->unique_id) ? 'selected' : '';
//         $options .= '<option value="' . $group->unique_id . '" ' . $isSelected . '>' . $group->group_name . '</option>';
//     }

//     return $options;
// }

function get_spare_group_id($selected = null)
{
    // Handle input formats: comma-separated string, array, or single value
    if (is_array($selected)) {
        $selectedArray = $selected;
    } elseif (is_string($selected) && str_contains($selected, ',')) {
        $selectedArray = explode(',', $selected);
    } elseif (!empty($selected)) {
        $selectedArray = [$selected];
    } else {
        $selectedArray = [];
    }

    $groups = DB::table('spare_group')
        ->where('is_active', 1)
        ->where('is_delete', 0)
        ->orderBy('group_name')
        ->get();

    $options = '<option value="">Select Group Name</option>';

    foreach ($groups as $group) {
        $isSelected = in_array($group->unique_id, $selectedArray) ? 'selected' : '';
        $options .= '<option value="' . $group->unique_id . '" ' . $isSelected . '>' . $group->group_name . '</option>';
    }

    return $options;
}

function get_spare_group_id_multi($selected = null, $arrayValues = null)
{
    // Handle input formats: comma-separated string, array, or single string
    if (is_array($arrayValues)) {
        $selectedArray = $arrayValues;
    } elseif (is_string($arrayValues) && str_contains($arrayValues, ',')) {
        $selectedArray = explode(',', $arrayValues);
    } elseif (!empty($arrayValues)) {
        $selectedArray = [$arrayValues];
    } else {
        $selectedArray = [];
    }

    $groups = DB::table('spare_group')
        ->where('is_active', 1)
        ->where('is_delete', 0)
        ->orderBy('group_name')
        ->get();

    $options = '<option value="">Select Group Name</option>';

    foreach ($groups as $group) {
        $isSelected = in_array($group->unique_id, $selectedArray) ? 'selected' : '';
        $options .= '<option value="' . $group->unique_id . '" ' . $isSelected . '>' . $group->group_name . '</option>';
    }

    return $options;
}

function staff_name($unique_id = '')
{
    $staffname = DB::table('staff')->select('unique_id', 'name', 'staff_id')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($staffname) {
        return $staffname;
    } else {
        print_r($staffname);
        return 0;
    }
}

function supplier_name($selected = null)
{
    $supplier = DB::table('supplier')
        ->where('is_active', 1)
        ->where('is_delete', 0)
        ->get();

    $options = '<option value="">Select Supplier</option>';

    foreach ($supplier as $suppliers) {
        $isSelected = ($selected == $suppliers->unique_id) ? 'selected' : '';
        $options .= '<option value="' . $suppliers->unique_id . '" ' . $isSelected . '>' . $suppliers->supplier_name . '</option>';
    }

    return $options;
}

function branch_name($selected = null)
{
    $branch = DB::table('branch')
        ->where('is_active', 1)
        ->where('is_delete', 0)
        ->get();

    $options = '<option value="">Select Branch</option>';

    foreach ($branch as $branchs) {
        $isSelected = ($selected == $branchs->unique_id) ? 'selected' : '';
        $options .= '<option value="' . $branchs->unique_id . '" ' . $isSelected . '>' . $branchs->branch_name . '</option>';
    }

    return $options;
}

function location_name($selected = null)
{
    $location = DB::table('location')
        ->where('is_active', 1)
        ->where('is_delete', 0)
        ->get();

    if ($location) {

        return $location;
    } else {
        print_r($location);
        return 0;
    }
}

function get_dept_name($unique_id = '')
{
    $department = DB::table('department')->select('unique_id', 'department')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($department) {

        return $department;
    } else {
        print_r($department);
        return 0;
    }
}

function get_stage($unique_id = '')
{

    $stage_name = DB::table('stage')->select('unique_id', 'stage')->where(['is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($stage_name) {

        return $stage_name;
    } else {
        print_r($stage_name);
        return 0;
    }
}

function get_dept_category()
{
    $dept_category = DB::table('department_category')->select('unique_id','department_category')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($dept_category) {
        return $dept_category;
    } else {
        print_r($dept_category);
        return 0;
    }
}

function get_staff_name()
{
    $staff_name = DB::table('staff_registration')->select('unique_id','name')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($staff_name) {
        return $staff_name;
    } else {
        print_r($staff_name);
        return 0;
    }
}
//source name
function get_source_name()
{
    $source_name = DB::table('source_name')->select('unique_id','source_name')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($source_name) {
        return $source_name;
    } else {
        print_r($source_name);
        return 0;
    }
}

function get_priority()
{
    $priority = DB::table('priority')->select('unique_id','priority')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($priority) {
        return $priority;
    } else {
        print_r($priority);
        return 0;
    }
}

function get_location($unique_id = '')
{
    $location = DB::table('location')->select('unique_id', 'location')->where(['is_active' => 1, 'is_delete' => 0])->orderBy('id', 'ASC')->get();

    if ($location) {

        return $location;
    } else {
        print_r($location);
        return 0;
    }
}