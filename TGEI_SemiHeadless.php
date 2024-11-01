<?php

/**
 * Plugin Name: TGEI Semi Headless
 * Description: Disables all pages on the backend unless specified not too
 * Version: 1.0
 * Stable: 1.0
 * Author: Too Good Enterprises Incorporated
 * Author URI: https://toogoodenterprises.com
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
namespace TooGoodEnterprisesInc\SemiHeadless;

defined("ABSPATH") || exit;

// import the settings class
require_once __DIR__."/TGEI_SemiHeadless_Settings.php";


class TGEI_SemiHeadless
{
  const ALLOW = "1";
  const BLOCK = false;
  const ON = "on";
  const OFF = "off";
  public static function register()
  {
    add_action("plugins_loaded", __NAMESPACE__."\TGEI_SemiHeadless::initialize", 10);
    register_activation_hook(__FILE__, __NAMESPACE__."\TGEI_SemiHeadless::activate");
    register_deactivation_hook(__FILE__, __NAMESPACE__."\TGEI_SemiHeadless::deactivate");
    register_uninstall_hook(__FILE__, __NAMESPACE__."\TGEI_SemiHeadless::uninstall");
    add_action("admin_menu", __NAMESPACE__."\TGEI_SemiHeadless_Settings::registerSettingsPage");

  }

  public static function initialize()
  {
    // register a post meta that will keep track whether the post or page is allowed
    register_post_meta(
      "",
      "tgei-semi-headless-allow",
      array(
        "type" => "boolean",
        "default" => false,
        "single" => true,
      )
    );

    // register a term meta that will keep track whether the taxonomy is allowed
    register_term_meta(
      "",
      "tgei-semi-headless-allow",
      array(
        "type" => "boolean",
        "default" => false,
        "single" => true
      )
    );

    // the function to check and redirect
    add_action("wp", __NAMESPACE__."\TGEI_SemiHeadless::redirect");

    // all other actions and filters for admin section only
    if(is_admin())
    {
      // initialize the admin settings
      add_action("init", __NAMESPACE__."\TGEI_SemiHeadless::initAdminSettings");

      // initialize the meta boxes in the post and page editor
      add_action( "add_meta_boxes", __NAMESPACE__."\TGEI_SemiHeadless_Settings::addMetaBox" );

      // add a link to settings in the plugin links
      add_filter( "plugin_action_links", __NAMESPACE__."\TGEI_SemiHeadless::pluginLinks", 10, 2 );
    }
  }

  public static function initAdminSettings()
  {

    $nonce = wp_create_nonce("tgei-semi-headless-nonce");
    $ajaxUrl = admin_url( "admin-ajax.php" );
    $inlineJS = ' 
      class TGEI_SemiHeadless_Data
      {
      static ajaxUrl = "'.esc_url($ajaxUrl).'";
      static nonce = "'.esc_js($nonce).'";
      }
    ';


    // enqueue the admin script
    wp_enqueue_script("TGEI_SemiHeadless_Admin_JS", plugins_url("TGEI-SemiHeadless/TGEI_SemiHeadless_Admin.js"),[], wp_rand(), true);

    // enqueue the inline script
    wp_add_inline_script("TGEI_SemiHeadless_Admin_JS", $inlineJS, "before");

    // enqueue the style
    wp_enqueue_style("TGEI_SemiHeadless_Settings_CSS", plugins_url("TGEI-SemiHeadless/TGEI_SemiHeadless_Settings.css"),[], wp_rand());
    $allPostTypes = get_post_types();
    // add field into quick edit form
    add_action( "quick_edit_custom_box",  __NAMESPACE__."\TGEI_SemiHeadless_Settings::renderToggleFieldQuickEdit", 10, 3 );

    // add a custom column in all post types
    foreach($allPostTypes as $postType)
    {
      // add custom column
      add_filter( "manage_".$postType."_posts_columns", __NAMESPACE__."\TGEI_SemiHeadless_Settings::addCustomColumnsPost" );
      
      // display value in column
      add_filter("manage_".$postType."_posts_custom_column", __NAMESPACE__."\TGEI_SemiHeadless_Settings::renderCustomColumnsPost", 10, 2);


      // make custom column sortable
      add_filter("manage_edit-".$postType."_sortable_columns", __NAMESPACE__."\TGEI_SemiHeadless_Settings::addCustomColumnsPost");

      // add bulk edit options
      add_filter(
        "bulk_actions-edit-".$postType,
        __NAMESPACE__."\TGEI_SemiHeadless::addBulkActions"
      );

      // handle bulk edit options
      add_filter(
        "handle_bulk_actions-edit-".$postType,
        __NAMESPACE__."\TGEI_SemiHeadless::handleBulkActionPost", 
        10, 
        3
      );
    }

    // sort custom column algorithm
    add_action( "pre_get_posts", __NAMESPACE__."\TGEI_SemiHeadless_Settings::sortCustomColumnAlgorithm" );
    // add filter for the custom column
    add_action( "restrict_manage_posts",  __NAMESPACE__."\TGEI_SemiHeadless_Settings::statusFilter" );

    // add meta box to the category screen
    $allTaxonomies = get_taxonomies();
    foreach($allTaxonomies as $taxonomy)
    {
      // display the form field
      add_action ( $taxonomy."_add_form_fields", __NAMESPACE__."\TGEI_SemiHeadless_Settings::addTaxonomyMetaBoxRender" );
      add_action ( $taxonomy."_edit_form_fields", __NAMESPACE__."\TGEI_SemiHeadless_Settings::editTaxonomyMetaBoxRender", 10, 2  );

      // save the changes
      add_action( "created_".$taxonomy, __NAMESPACE__."\TGEI_SemiHeadless_Settings::saveStatusTaxonomy" );
      add_action( "edited_".$taxonomy, __NAMESPACE__."\TGEI_SemiHeadless_Settings::saveStatusTaxonomy" );

      // add custom column in the manage screens
      add_filter( "manage_edit-".$taxonomy."_columns", __NAMESPACE__."\TGEI_SemiHeadless_Settings::addCustomColumnsPost" );
      
      // make custom column sortable
      add_filter ("manage_edit-".$taxonomy."_sortable_columns", __NAMESPACE__."\TGEI_SemiHeadless_Settings::addCustomColumnsPost");

      // populate the column with respective data
      add_filter("manage_".$taxonomy."_custom_column", __NAMESPACE__."\TGEI_SemiHeadless_Settings::renderCustomColumnsTaxonomy", 10, 3);

      // add bulk action options
      add_filter("bulk_actions-edit-".$taxonomy, __NAMESPACE__."\TGEI_SemiHeadless::addBulkActions");

      // handle bulk action optijons
      add_filter(
        "handle_bulk_actions-edit-".$taxonomy,
        __NAMESPACE__."\TGEI_SemiHeadless::handleBulkActionTaxonomy", 
        10, 
        3
      );

    }

    add_action( "save_post", __NAMESPACE__."\TGEI_SemiHeadless_Settings::saveStatusPost" );

    // load admin notices
    add_action( "admin_notices", __NAMESPACE__."\TGEI_SemiHeadless::adminNotices");
    
  }

  public static function activate()
  {
    //check if our settings exist in wp_options
    //if not, add them to the table
    $redirUrl = get_option("tgei-semi-headless-field_redir_url", false);
    if(!$redirUrl)
    {
      add_option("tgei-semi-headless-field_redir_url", "");
    }

    $redirUrl404 = get_option("tgei-semi-headless-field_redir_url_404", false);
    if(!$redirUrl404)
    {
      add_option("tgei-semi-headless-field_redir_url_404", "");
    }

    $allowSearch = get_option("tgei-semi-headless-field_allow_search", false);
    if(!$allowSearch)
    {
      add_option("tgei-semi-headless-field_allow_search", false);
    }

    $allowHomepage = get_option("tgei-semi-headles-field_allow_homepage", false);
    if(!$allowHomepage)
    {
      add_option("tgei-semi-headless-field_allow_homepage", false);
    }
  }

  public static function deactivate()
  {
  }

  public static function uninstall()
  {
    delete_option("tgei-semi-headless-field_redir_url");
    delete_option("tgei-semi-headless-field_redir_url_404");
    delete_option("tgei-semi-headless-field_allow_search");
    delete_option("tgei-semi-headless-field_allow_homepage");

    // delete all tgei-semi-headless-allow meta key in posts
    delete_post_meta_by_key("tgei-semi-headless-allow");

    // delete all tgei-semi-headless-allow meta key for terms
    delete_metadata( "term", null, "tgei-semi-headless-allow", "", true );
  }

    
  /**
   * This function determines whether we are redirecting to the headless page
   * or not based on an allow list
   */
  public static function redirect()
  {
    // allow request to proceed as normal if it's a rest api request or is admin
    if(TGEI_SemiHeadless::isRestRequest() || is_admin())
    {
      return;
    }
    else if(is_404())
    {
      TGEI_SemiHeadless::redirect404();
    }
    // check whether it's the homepage
    else if(is_home())
    {
      $allowHome = get_option("tgei-semi-headless-field_allow_homepage");
      if($allowHome !== TGEI_SemiHeadless::ON)
      {
        // to prevent an infinite loop bug
        // ensure that the redirect url is not the home url
        $redirUrl = rtrim(get_option("tgei-semi-headless-field_redir_url"), "/");
        if($redirUrl == get_home_url())
        {
          wp_die("TGEI Semi Headless ERROR: A redirect url is same url as homepage url which is set to be blocked<br />Either allow the wordpress homepage or use a different url for redirection.");
        }
        else
        { 
          header("Location: ".$redirUrl);
        }
      }
    }
    // check whether it's a search request
    else if(is_search())
    {
      $allowSearch = get_option("tgei-semi-headless-field_allow_search");
      if($allowSearch !== TGEI_SemiHeadless::ON)
      {
        TGEI_SemiHeadless::redirectDefault();
      }
    }
    // check whether request is a post or page
    else if(is_singular())
    {
      global $wp;
      global $post;
      $allow = get_post_meta($post->ID, "tgei-semi-headless-allow", true);
      if(!$allow)
      {
        TGEI_SemiHeadless::redirectDefault();
      }
    }
    // check whether it's a taxonomy archive page
    else if(is_archive() || is_category() || is_tag() || is_tax())
    {
      $qo = get_queried_object();
      $allow = false;
      if($qo instanceof \WP_Term)
      {
        $taxId = get_queried_object()->term_id;
        $allow = get_term_meta($taxId, "tgei-semi-headless-allow", true);
      }
      else if($qo instanceof \WP_Post)
      {
        $postId = get_queried_object()->post_id;
        $allow = get_post_meta($postId, "tgei-semiheadless-allow", true);
      }
      if(!$allow)
      {
        TGEI_SemiHeadless::redirectDefault();
      }
    }
    // block author archives
    else if(is_author())
    {
      TGEI_SemiHeadless::redirectDefault();
    }
  }

  /**
   * redirectDefault
   * set header to the default redirect url
   */
  public static function redirectDefault()
  {
    $redirUrl = get_option("tgei-semi-headless-field_redir_url");
    if($redirUrl != "")
    {
      header("Location: ".$redirUrl);
    }
  }

  /**
   * redirect404
   * set head to the 404 redirect url
   */
  public static function redirect404()
  {
    $redir404 = get_option("tgei-semi-headless-field_redir_url_404");
    if($redir404 != "")
    {
      header("Location: ".$redir404);
    }
  }

  /**
   * Check if request is a rest api request
   * based on https://wordpress.stackexchange.com/a/356946
   */
  public static function isRestRequest()
  {
    if ( empty( $_SERVER["REQUEST_URI"] ) ) {
        // Probably a CLI request
        return false;
    }

    $rest_prefix = trailingslashit( rest_get_url_prefix() );
    $is_rest_api_request = strpos( sanitize_url(wp_unslash($_SERVER["REQUEST_URI"])), $rest_prefix ) !== false;
    return $is_rest_api_request;
  }


  /**
   * Add the allow and block options
   * to the Bulk Actions dropdown menu
   */
  public static function addBulkActions($bulkActions)
  {
    $bulkActions["tgei-semi-headless-bulk-allow"] = "TGEI Semi Headless Allow";
    $bulkActions["tgei-semi-headless-bulk-block"] = "TGEI Semi Headless Block";
    return $bulkActions;
  }

  /**
   * wrapper function for handleBulkAction
   * for post type
   */
  public static function handleBulkActionPost($redirectUrl, $action, $postIds)
  {
    return TGEI_SemiHeadless::handleBulkAction($redirectUrl, $action, $postIds, "post");
  }

  /**
   * wrapper function for handleBulkAction
   * for taxonomy type
   */
  public static function handleBulkActionTaxonomy($redirectUrl, $action, $taxIds)
  {
    return TGEI_SemiHeadless::handleBulkAction($redirectUrl, $action, $taxIds, "taxonomy");
  }

  /**
   * update the post or term meta respectively
   */
  public static function handleBulkAction($redirectUrl, $action, $Ids, $type)
  {
    // ensure type is post or taxonomy
    // if not, exit function
    if($type != "post" && $type != "taxonomy")
    {
      return $redirectUrl;
    }

    $nonce = wp_create_nonce("tgei-semiheadless-nonce");

    if($action == "tgei-semi-headless-bulk-allow")
    {
      foreach($Ids as $id)
      {
        if($type == "post")
        {
          update_post_meta($id, "tgei-semi-headless-allow", TGEI_SemiHeadless::ALLOW);
        }
        else
        {
          update_term_meta($id, "tgei-semi-headless-allow", TGEI_SemiHeadless::ALLOW);
        }
      }
      // remove query from url since it's processed
      $redirectUrl = remove_query_arg("tgei-semi-headless-bulk-allow", $redirectUrl);

      // if we allowed items, set query to show admin notice
      if(count($Ids) > 0)
      {
        $redirectUrl = add_query_arg("tgei-semi-headless-bulk-action", "allow", $redirectUrl);
      }
    }
    else if($action == "tgei-semi-headless-bulk-block")
    {
      foreach($Ids as $id)
      {
        if($type == "post")
        {
          update_post_meta($id, "tgei-semi-headless-allow", TGEI_SemiHeadless::BLOCK);
        }
        else
        {
          update_term_meta($id, "tgei-semi-headless-allow", TGEI_SemiHeadless::BLOCK);
        }
      }
      // remove query from url since it's processed
      $redirectUrl = remove_query_arg("tgei-semi-headless-bulk-block", $redirectUrl);

      // if we blocked items, set query to show admin notice
      if(count($Ids) > 0)
      {
        $redirectUrl = add_query_arg("tgei-semi-headless-bulk-action", "block", $redirectUrl);
      }
    }
    // add the nonce field
    $redirectUrl = add_query_arg("tgei-semiheadless-nonce", $nonce, $redirectUrl);
    return $redirectUrl;
  }

  // add admin notices for bulk editing
  public static function adminNotices()
  {
     // nonce check
    if (!array_key_exists("tgei-semiheadless-nonce", $_GET ) || ! wp_verify_nonce( sanitize_key(wp_unslash($_GET[ "tgei-semiheadless-nonce"])), "tgei-semiheadless-nonce" ) ) 
    {
      return;
    }
    if(array_key_exists("tgei-semi-headless-bulk-action", $_GET)){
      if($_GET["tgei-semi-headless-bulk-action"] == "allow")
      {
        echo '
          <div id="message" class="notice notice-success is-dismissible tgei-admin-notice">TGEI Semi Headless: Selected items have been changed to Allow</div>
        ';
      }
      else if($_GET["tgei-semi-headless-bulk-action"] == "block")
      {
        echo '
          <div id="message" class="notice notice-success is-dismissible tgei-admin-notice">TGEI Semi Headless: Selected items have been changed to Block</div>
        ';
      }
    }
  }

  // add settings link of the plugin in plugin section
  public static function pluginLinks($links, $file)
  {
    // check if it's for this plugin
    if ( $file == plugin_basename(dirname(__FILE__) . "/TGEI_SemiHeadless.php") ) 
    {
      $helpLink = '<a href="'.get_admin_url(null, "tools.php?page=tgei-semi-headless-help").'">Help</a>';
      array_unshift($links, $helpLink);
      $settingsLink = '<a href="'.get_admin_url(null, "tools.php?page=tgei-semi-headless-settings").'">Settings</a>';
      array_unshift($links, $settingsLink);

    }
    return $links;
  }
}

TGEI_SemiHeadless::register();
