<?php
/**
 * This file is part of TGEI Semi Headless
 * TGEI Semi Headless is licensed with GPLv2
 * Copyright (C) 2024  Too Good Enterprises Inc.
 */
namespace TooGoodEnterprisesInc\SemiHeadless;

class TGEI_SemiHeadless_Settings
{
  // Setting Tabs
  const TABS = array(
      "settings" => "General Settings",
      "help" => "Help"
    );

/**
   * register the settings of the plugin and the menu for the plugin
   */
  public static function registerSettingsPage()
  {
    // register the menu page
    add_submenu_page("tools.php", "TGEI Semi Headless", "TGEI Semi Headless", "manage_options", "tgei-semi-headless-settings", __NAMESPACE__."\TGEI_SemiHeadless_Settings::renderGeneralSettingsPage");
    add_submenu_page("", "TGEI Semi Headless Help", "TEST", "manage_options", "tgei-semi-headless-help", __NAMESPACE__."\TGEI_SemiHeadless_settings::renderHelpPage");

    // register settings
    TGEI_SemiHeadless_Settings::registerSettings();

    // add the sections
    add_settings_section(
      "tgei-semi-headless_general",
      "",
      __NAMESPACE__."\TGEI_SemiHeadless_Settings::generalSettingsSection",
      "tgei-semi-headless"
    );
    
    // add the setting fields
    add_settings_field(
      "tgei-semi-headless-field_redir_url",
      "Redirect URL",
      __NAMESPACE__."\TGEI_SemiHeadless_Settings::renderTextField",
      "tgei-semi-headless",
      "tgei-semi-headless_general",
      array(
        "tooltip" => "The address all blocked wordpress addresses will redirect to. Leave blank to disable redirect.",
        "name" => "tgei-semi-headless-field_redir_url"
      )
    );

    add_settings_field(
      "tgei-semi-headless-field_redir_url_404",
      "Error 404 Redirect URL",
      __NAMESPACE__."\TGEI_SemiHeadless_Settings::renderTextField",
      "tgei-semi-headless",
      "tgei-semi-headless_general",
      array(
        "tooltip" => "The address that you want all 404 errors to redirect to. Leave blank to use default Wordpress 404 error page.",
        "name" => "tgei-semi-headless-field_redir_url_404"
      )
    );

    add_settings_field(
      "tgei-semi-headless-field_allow_homepage",
      "Homepage",
      __NAMESPACE__."\TGEI_SemiHeadless_Settings::renderToggleFieldSettings",
      "tgei-semi-headless",
      "tgei-semi-headless_general",
      array(
        "tooltip" => "If allowed, this wordpress homepage will be accessible.",
        "name" => "tgei-semi-headless-field_allow_homepage"
      )
    );

    add_settings_field(
      "tgei-semi-headless-field_allow_search",
      "Default Search Results Page",
      __NAMESPACE__."\TGEI_SemiHeadless_Settings::renderToggleFieldSettings",
      "tgei-semi-headless",
      "tgei-semi-headless_general",
      array(
        "tooltip" => "If allowed, the default search results page will be accessible.",
        "name" => "tgei-semi-headless-field_allow_search"
      )
    );

  }

  /**
   * register settings
   */
  public static function registerSettings()
  {
    register_setting(
      "tgei-semi-headless",
      "tgei-semi-headless-field_redir_url",
      array(
        "type" => "string",
        "default" => "",
        "sanitize_callback" => function($value)
        {
          return TGEI_SemiHeadless_Settings::validateUrlField($value, "tgei-semi-headless-field_redir_url", "Redirect Url");
        }
      )
    );

    register_setting(
      "tgei-semi-headless",
      "tgei-semi-headless-field_redir_url_404",
      array(
        "type" => "string",
        "default" => get_site_url(),
        "sanitize_callback" => function($value)
        {
          return TGEI_SemiHeadless_Settings::validateUrlField($value, "tgei-semi-headless-field_redir_url_404", "Error 404 Redirect Url");
        }
      )
    );

    register_setting(
      "tgei-semi-headless",
      "tgei-semi-headless-field_allow_search",
      array(
        "type" => "boolean",
        "default" => false
      )
    );

    register_setting(
      "tgei-semi-headless",
      "tgei-semi-headless-field_allow_homepage",
      array(
        "type" => "boolean",
        "default" => false
      )
    );
  }

  /**
   * settingsPageHeader
   */
  public static function settingsPageHeader($currentTab)
  {
    wp_enqueue_script("TGEI_SemiHeadless_Settinggs_JS", plugins_url("TGEI-SemiHeadless/TGEI_SemiHeadless_Settings.js"),[], wp_rand(), true);
    wp_enqueue_style("TGEI_SemiHeadless_Settings_Help_CSS", plugins_url("TGEI-SemiHeadless/TGEI_SemiHeadless_Settings_Help.css"),[], wp_rand());
    settings_errors(); // to enable admin notices

    echo ' 
      <h1>TGEI Semi Headless</h1>
      <nav class="nav-tab-wrapper">
    ';
    foreach( TGEI_SemiHeadless_Settings::TABS as $tab => $name ){
      $url = add_query_arg( array( "page" => "tgei-semi-headless-".$tab), "" );
      // create a label and the radio button
      echo '<a class="nav-tab '.($tab == $currentTab ? "nav-tab-active" : "").'" href="'.esc_url($url).'">'.esc_html($name).'</a>';
    }
    echo '
      </nav>
      <section id="container" style="display: block; width: 100%; box-sizing: border-box; padding-right: 15px;">
    ';
  }

  /**
   * settingsPageFooter
   */
  public static function settingsPageFooter($currentTab)
  {
    echo '</section>';
    echo '<div id="tgei-shadowbox" class="tgei-hide"></div>';
  }

  /**
   * General Settings Section callback method
   * echos out any content at the top of the section (between heading and fields)
   */
  public static function generalSettingsSection()
  {
    // blank
  }

  /**
   * Render the General Settings Page
   */
  public static function renderGeneralSettingsPage()
  {
    TGEI_SemiHeadless_Settings::settingsPageHeader("settings");
    echo '<form method="post" action="options.php">';
    settings_fields("tgei-semi-headless");
    echo '<table class="form-table" id="tgei-semiheadless-settings-table">';
    do_settings_sections("tgei-semi-headless");
    echo '</table>';
    submit_button();
    echo '</form>';
    TGEI_SemiHeadless_Settings::settingsPageFooter("settings");
  }

  public static function renderHelpPage()
  {
    TGEI_SemiHeadless_Settings::settingsPageHeader("help");
    $images = array();
    $images["editPost"] = esc_url(plugin_dir_url(__FILE__)."assets/screenshot-1.png");
    $images["quickEdit"] = esc_url(plugin_dir_url(__FILE__)."assets/screenshot-2.png");
    $images["bulkEdit"] = esc_url(plugin_dir_url(__FILE__)."assets/screenshot-3.png");
    $images["editTaxonomy"] = esc_url(plugin_dir_url(__FILE__)."assets/screenshot-4.png");
    $images["editTaxonomyQuick"] = esc_url(plugin_dir_url(__FILE__)."assets/screenshot-5.png");


    echo '
       <h2>Frequently Asked Questions</h2>
      <ul class="tgei-faq">
        <li>
          <label>
            <input type="checkbox" />
            <span>
              What is TGEI Semi Headless?
            </span>
            <div>
              <p>TGEI Semi Headless is a tool that can assist in the development of headless wordpress sites when a website does not need to go fully headless. Perhaps for some website features, it may be better to use a wordpress plugin than reimplement a headless version.</p>
            </div>
          </label>
        </li>
        <li>
          <label>
            <input type="checkbox" />
            <span>
              What frontend wordpress features is TGEI Semi Headless able to block?
            </span>
            <div>
              <p>TGEI Semi Headless an block posts, pages, custom posts, archive pages, search pages, and the homepage.</p>
            </div>
          </label>
        </li>
        <li>
          <label>
            <input type="checkbox" />
            <span>
              How do I unblock or block a post or page?
            </span>
            <div>
              <h3>Method 1: While Editing</h3>
              <p>
                While editing a page or post, there is switch in the settings pane to allow or block the post or page. Remember to save the page.
              </p>
              <p>
                Note: This method may not work with custom post types
              </p>
              <p>
                <img src="'.esc_url($images["editPost"]).'" class="tgei-screenshot" onClick="TGEI_SemiHeadless_Settings.showShadowBox(event, \'tgei-shadowbox\', this);" />
              </p>
              <h3>Method 2: Quick Edit</h3>
              <p>
                In the Admin section of wordpress that lists the posts or pages, click on quick edit and toggle the status from allow to block or vice versa. Remember to save the changes.
              </p>
              <p>
                <img src="'.esc_url($images["quickEdit"]).'" class="tgei-screenshot" onClick="TGEI_SemiHeadless_Settings.showShadowBox(event, \'tgei-shadowbox\', this);" />
              </p>
            </div>
          </label>
        </li>
        <li>
          <label>
            <input type="checkbox" />
            <span>
              Can I change the status of multiple items at once?
            </span>
            <div>
              <p>Yes. In the Wordpress Admin Area, go to the area that lists all the items. This could be the posts section, tag section, custom post type section, etc. Check the items you want to modify and under bulk actions, choose to allow or block the selected items and click apply.</p>
              <p>
                <img src="'.esc_url($images["bulkEdit"]).'" class="tgei-screenshot" onClick="TGEI_SemiHeadless_Settings.showShadowBox(event, \'tgei-shadowbox\', this);" />
              </p>
            </div>
          </label>
        </li>
        <li>
          <label>
            <input type="checkbox" />
            <span>
              How can I change the status of archive pages for taxonomies such as categories and tags?
            </span>
            <div>
              <h3>Method 1: Edit Taxonomy</h3>
              <p>
              Go to the taxonomies edit page and toggle the status.
              </p>
              <p>
                <img src="'.esc_url($images["editTaxonomy"]).'" class="tgei-screenshot" onClick="TGEI_SemiHeadless_Settings.showShadowBox(event, \'tgei-shadowbox\', this);" />
              </p>
              <h3>Method 2: Quick Edit</h3>
              <p>
                Use the quick edit in the taxonomy page.
              </p>
              <p>
                <img src="'.esc_url($images["editTaxonomyQuick"]).'" class="tgei-screenshot" onClick="TGEI_SemiHeadless_Settings.showShadowBox(event, \'tgei-shadowbox\', this);" />
              </p>
            </div>
          </label>
        </li>
        <li>
          <label>
            <input type="checkbox" />
            <span>
              How do I block search result pages?
            </span>
            <div>
              <p>Go to Settings and set <em>Default Search Result Page</em> to block. Remember to save the changes.</p>
            </div>
          </label>
        </li>
        <li>
          <label>
            <input type="checkbox" />
            <span>
              How do I fix an infinite loop error?
            </span>
            <div>
              <p>An infinite loop occurs when the page at the redirect url is also blocked. To fix, change the redirect url to a page that not blocked.
              </p>
            </div>
          </label>
        </li>
      </ul>
    ';
    TGEI_SemiHeadless_Settings::settingsPageFooter("help");
  }

  /**
   * renders a text field
   * @param $args is an array that contains tooltip, name, and value
   */
  public static function renderTextField(array $args)
  {
    $tooltipText = esc_attr($args["tooltip"]);
    $value   = get_option( $args["name"]);
    $name = esc_attr($args["name"]);

    echo '
      <input type="text" value="'.esc_attr($value).'" name="'.esc_attr($name).'" /><div class="tgei-tooltip">?<div class="tgei-tooltipText">'.esc_html($tooltipText).'</div>
    ';
  }

  /**
   * gets the value of the settings field and renders the toggle
   * gets the status of the field and renders accordingly
   * @param args that contains the name of the field and the tooltip text
   */
  public static function renderToggleFieldSettings(array $args)
  {
    $value = get_option( $args["name"]);
    $checked = ($value === TGEI_SemiHeadless::ALLOW || $value === TGEI_SemiHeadless::ON) ? "checked" : "";
    TGEI_SemiHeadless_Settings::renderToggleField($args["name"], $args["name"], $checked, $args["tooltip"]);
  }

  /**
   * renders the status toggle in a quick edit screen
   */
  public static function renderToggleFieldQuickEdit($columnName, $postType, $taxType)
  {
    // prevent rendering this meta box if not for our column
    // without check, will render the meta box many times
    if($columnName != "tgei-semi-headless")
    {
      return;
    }

    echo '
    <fieldset class="inline-edit-col-left">
      <legend class="inline-edit-legend">
        TGEI Semi Headless
      </legend>
      <div class="inline-edit-col">
    ';
    TGEI_SemiHeadless_Settings::renderToggleField("tgei-semi-headless-allow", "tgei-semi-headless-allow", false, "By default, this post/page is blocked and redirects to the address specified in the settings. Select Allow for this page to be accessible.");
    echo '
        </div>
      </fieldset>
    ';

  }
  /**
   * render a toggle field
   * @param name the name of the input
   * @param id the id of the field element
   * @param checked true for checked
   * @param text for the tooltip, tooltip hidden if false
   */
  public static function renderToggleField($name, $id, $checked, $tooltipText)
  {
    echo '
      <label class="tgei-toggle" for="'.esc_attr($id).'_ui">
        <input type="checkbox" id="'.esc_attr($id).'_ui" onchange="document.getElementById(\''.esc_attr($id).'\').value = document.getElementById(\''.esc_attr($id).'_ui\').checked ? \'on\' : \'off\';" '.esc_attr($checked).' />
        <div class="status block">Block</div>
        <div class="connector">
          <span class="switch"></span>
        </div>
        <div class="status allow">Allow</div>
      </label>
    ';
    // checkbox does not submit a value when not checked
    // use hidden input so that field is always submitted
    echo '<input type="hidden" name="'.esc_attr($name).'" id="'.esc_attr($id).'" value="off" />';


    if($tooltipText !== false)
    {
      echo '
      <div class="tgei-tooltip">?<div class="tgei-tooltipText">'.esc_html($tooltipText).'</div></div>
      ';
    }
    // add the nonce field
    TGEI_SemiHeadless_Settings::createNonceField();
  }

  /**
   * Displays the drop down menu in a post
   * or page edit screen to set the status
   * of the allow list for this post/page
   */
  public static function postMetaBoxRender()
  {
    global $post;
    $checked = get_post_meta($post->ID, "tgei-semi-headless-allow", true) ? "checked" : "";
    
    $tgeiNonce = wp_create_nonce( "tgei-semiheadless-nonce");  
    echo '
      <p>By default, this post/page is blocked and redirects the address specified in the settings. Select allow for this page to be accessible.</p>
    ';
    TGEI_SemiHeadless_Settings::renderToggleField("tgei-semi-headless-allow", "tgei-semi-headless-allow", $checked, false);
  }

  /**
   * renders the drop down option for the
   * category page
   */
  public static function editTaxonomyMetaBoxRender($term, $taxonomy)
  {
    $checked = get_term_meta( $term->term_id, "tgei-semi-headless-allow", true) ? "checked" : "";
    echo '
      <tr class="form-field">
      <th scope="row">
      <label for="tgei-semi-headless-allow">TGEI Semi Headless</label>
      </th>
      <td>
    ';
    TGEI_SemiHeadless_Settings::renderToggleField("tgei-semi-headless-allow", "tgei-semi-headless-allow", $checked, "By default, this post/page is blocked and redirects to the address specified in the settings. Select Allow for this page to be accessible.");
    echo '
      </td>
      </tr>
    ';
  }
  /**
   * render meta box for taxonomy edit screen
   */
  public static function addTaxonomyMetaBoxRender()
  {
    echo '
      <div class="form-field term-wrap">
      TGEI Semi Headless<br />
    ';
    TGEI_SemiHeadless_Settings::renderToggleField("tgei-semi-headless-allow", "tgei-semi-headless-add-taxonomy", false, "By default, the archive page for this tag/category is blocked and redirects to the address specified in the settings. Select Allow for this page to be accessible.");
    echo '</div>';
  }


  /**
   * save changes to the meta when a post is saved
   */
  public static function saveStatusPost($postId)
  {
    // check we have a tgei-semi-headless-allow field
    // if not, skip
    if(array_key_exists("tgei-semi-headless-allow", $_POST)) 
    {
      // nonce check
      if (!array_key_exists("tgei-semiheadless-nonce", $_POST ) || ! wp_verify_nonce( sanitize_key(wp_unslash($_POST[ "tgei-semiheadless-nonce"])), "tgei-semiheadless-nonce" ) ) {
        return;
      }
      $allow = TGEI_SemiHeadless::BLOCK;

      // check if status set to on
      if($_POST["tgei-semi-headless-allow"] == "on")
      {
        $allow = TGEI_SemiHeadless::ALLOW;
      }
      update_post_meta($postId,"tgei-semi-headless-allow", $allow);   
    }
  }

  /**
   * save changes to the meta field when a taxonomy is created or edited
   */
  public static function saveStatusTaxonomy($taxId)
  {
    if(array_key_exists("tgei-semi-headless-allow", $_POST))
    {
      // nonce check
      if ( !array_key_exists("tgei-semiheadless-nonce", $_POST ) || ! wp_verify_nonce( sanitize_key(wp_unslash($_POST[ "tgei-semiheadless-nonce"])), "tgei-semiheadless-nonce" ) ) {
        return;
      }

      $allow = TGEI_SemiHeadless::BLOCK;
      if($_POST["tgei-semi-headless-allow"] == "on")
      {
        $allow = TGEI_SemiHeadless::ALLOW;
      }
      update_term_meta($taxId, "tgei-semi-headless-allow", $allow);
    }
  }

  /**
   * add TGEI Semi Headless as a custom column in the all posts/pages/category page
   */
  public static function addCustomColumnsPost($columns)
  {
    $columns[ "tgei-semi-headless" ] = "TGEI Semi Headless";
	  return $columns;
  }

  /**
   * render the values of the semi headless status
   */
  public static function renderCustomColumnsPost($columnName, $postId)
  {
    if($columnName == "tgei-semi-headless")
    {
      $allow = get_post_meta($postId, "tgei-semi-headless-allow", true);
      echo $allow ? "Allow" : "Block";
    }
  }
  /**
   * render the values of the semi headless status
   */
  public static function renderCustomColumnsTaxonomy($string, $columnName, $taxId)
  {
    if($columnName == "tgei-semi-headless")
    {
      $allow = get_term_meta($taxId, "tgei-semi-headless-allow", true);
      echo $allow ? "Allow" : "Block";
    }
  }

  /**
   * add a filter option to the manage post page
   */
  public static function statusFilter()
  {
    
    $selected = array();
    $selected["all"] = "";
    $selected["allow"] = "";
    $selected["block"] = "";

    // nonce check
    if ( array_key_exists("tgei-semiheadless-nonce", $_GET ) && wp_verify_nonce( sanitize_key(wp_unslash($_GET[ "tgei-semiheadless-nonce"])), "tgei-semiheadless-nonce" ) ) 
    {
      $option = array_key_exists("tgei-semi-headless-status", $_GET) ? sanitize_text_field(wp_unslash($_GET["tgei-semi-headless-status"])) : "all";
      $selected[$option] = "selected";
    }
    echo '
      <select name="tgei-semi-headless-status">
        <option value="all" '.esc_attr($selected["all"]).'/>TGEI Semi Headless</option>
        <option value="allow" '.esc_attr($selected["allow"]).'/>Allow</option>
        <option value="block" '.esc_attr($selected["block"]).'/>Block</option>
      </select>
    ';
    TGEI_SemiHeadless_Settings::createNonceField();

  }

  /**
   * sortCustomColumnAlgorithm
   * based on: https://wordpress.stackexchange.com/a/293403
   */
  public static function sortCustomColumnAlgorithm($query)
  {
    {
    $orderby = $query->get( 'orderby' );

    if ( 'TGEI Semi Headless' == $orderby ) {

        $meta_query = array(
            'relation' => 'OR',
            array(
                'key' => 'tgei-semi-headless-allow',
                'compare' => 'NOT EXISTS', // see note above
            ),
            array(
                'key' => 'tgei-semi-headless-allow',
            ),
        );

        $query->set( 'meta_query', $meta_query );
        $query->set( 'orderby', 'meta_value' );
    }
}
  }
  
  /**
   *  add post meta box to the post and page edit screens
   */
  public static function addMetaBox()
  {

    $allPostTypes = get_post_types();
    add_meta_box(
      "tgei-semi-headless",
      "TGEI Semi Headless",
      __NAMESPACE__."\TGEI_SemiHeadless_Settings::postMetaBoxRender",
      $allPostTypes,
      "side",
    );
  }
  
  /**
   * create the nonce field
   */
  public static function createNonceField($name="tgei-semiheadless-nonce")
  {
    $tgeiNonce = wp_create_nonce($name);
    echo '
      <input type="hidden" name="'.esc_attr($name).'" value="'.esc_attr($tgeiNonce).'" />
    ';

  }

  /**
   * returns true if $input is a valid url
   */
  public static function validUrl($input)
  {
    $parsedUrl = wp_parse_url($input);
    // parse_url returns false if seriously malformed 
    if($parsedUrl)
    {
      // valid url must have a scheme and host defined
      if(array_key_exists("scheme", $parsedUrl)  && array_key_exists("host", $parsedUrl))
      {
        // scheme must be http or https
        if($parsedUrl["scheme"] == "http" || $parsedUrl["scheme"] == "https")
        {
          return true;
        }
      }
    }
    return false;
  }

  /**
   * validate url entered into a url field
   */
  public static function validateUrlField($input, $field, $name)
  {
    if($input != "" && !TGEI_SemiHeadless_Settings::validUrl($input))
    {
      // get the currently saved value
      $input = get_option($field);
      add_settings_error("tgei-semi-headless-field_redir_url", "tgei-semi-headless-field_redir_url", $name." is invalid!", "error");
    }
    return $input;
  }
}
