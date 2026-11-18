<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class MenuServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
    {
      $session_val = session('user_type');
      // dd($session_val);

        // Load the menu data from the JSON file
        $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
        $menuData = json_decode($verticalMenuJson);

        // Get the current authenticated user

        // Filter the menu based on user type
        $filteredMenu = $this->filterMenuByUserType($menuData, $session_val);

        // Share the filtered menu data with all views
        View::share('menuData', $filteredMenu);
    }

    /**
     * Filter menu items based on the user type.
     *
     * @param object $menuData
     * @param string $userType
     * @return object
     */
    private function filterMenuByUserType($menuData, $userType)
    {
        foreach ($menuData->menu as $key => $menu) {
            // Assuming each menu item has a 'user_types' array specifying which user types can see it
            if (isset($menu->user_types) && !in_array($userType, $menu->user_types)) {
                unset($menuData->menu[$key]);
            }

            // Filter submenu items if they exist
            if (isset($menu->submenu)) {
                foreach ($menu->submenu as $subKey => $submenu) {
                    if (isset($submenu->user_types) && !in_array($userType, $submenu->user_types)) {
                        unset($menuData->menu[$key]->submenu[$subKey]);
                    }
                }

                // Remove the submenu property if it is empty after filtering
                if (empty($menuData->menu[$key]->submenu)) {
                    unset($menuData->menu[$key]->submenu);
                }
            }
        }

        return $menuData;
    }
}