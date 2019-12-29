# Wordpress fragment caching plugin

##### Table of Contents  
- [Introduction](#introduction)  
- [Features](#features)  
- [Pictures](#pictures) 
- [Installation](#installation) 
- [Configurations](#configurations)
- [How use it?](#how-use-it) 
   * [Cache creation](#cache-creation) 
        - [Method 1: Direct](#method-1-direct) 
        - [Method 2: Indirect](#method-2-indirect) 
   * [Cache cleanup](#cache-cleanup) 
        - [Manual](#manual) 
        - [Using hooks](#using-hooks) 
        - [Using functions](#using-functions)
- [Cleanup cases](#cleanup-cases) 
   * [Full](#full) 
   * [Partial](#partial) 
- [Hooks](#hooks) 
   * [Actions](#actions) 
   * [Filters](#filters)
- [Parametres](#parametres) 
- [Expiration constants](#expiration-constants) 
- [Database table](#database-table) 
- [Cache files](#cache-files) 
- [Next updates](#next-updates) 

## Introduction:
Simple fragment caching wordpress plugin for developers
- To use it in themes, plugins creation
- For speed up your themes or plugins
- Reduce server load

## Features:
- Seperate fragments by users role: admin, users & visitors.
- Seperate fragments by device: mobile, pc.
- Seperate fragments by post id,
- Seperate fragments by GPDR
- Seperate fragments by custom dynamic code using reference parameter 
- Can clean up all fragments.
- Can clean only one, or more fragments.
- Can control expiration time
- Can preload fragments.
- Update fragments when post, widget, menu ... etc updated.

... and more

## Pictures:
![Image of Option](https://github.com/Fethienv/jaw-fragment-caching/blob/master/assets/img/options.PNG?raw=true)
![Image of Fragments](https://github.com/Fethienv/jaw-fragment-caching/blob/master/assets/img/shows_fragments.PNG?raw=true)
![Image of Show Fragments](https://github.com/Fethienv/jaw-fragment-caching/blob/master/assets/img/show_fragments_cache_files.PNG?raw=true)
![Image of delete Fragments](https://github.com/Fethienv/jaw-fragment-caching/blob/master/assets/img/delete_fragments.PNG?raw=true)

## Installation:
Admin dashboard -> plugins -> add new  -> upload 

## Configurations:
* if you want change dirs: edit file general_config.php and change values
* if you want to change expiration time or disable fragment caching : Admin dashboard -> Tools -> Fragment caching -> option and un check status 

## How use it?
### Cache creation
#### Method 1: Direct
```

if (!jaw_get_cache_fragment('Section_name', 'Refernce_in_section','JAW_SPECIFIC_1',true,true)) {
     jaw_start_fragment_caching();
     
     // your code
     
     jaw_set_cache_fragment('Section_name', 'Refernce_in_section','JAW_SPECIFIC_1',true,true);
}

```
#### Method 2: Indirect
```

function get_template( $template_path, $template_name, $cached = true ) {
  
    $last_fragment_caching_status = FRAGMENT_CACHING_STATUS;
    if(!$cached){
        runkit_constant_redefine("FRAGMENT_CACHING_STATUS", False);
    }
    
    // first fragment part
    if (!jaw_get_cache_fragment($template_name, '1','JAW_RARLY',true,true)) {
        jaw_start_fragment_caching();

        /// your template code or functions
        
        jaw_set_cache_fragment($template_name, '1','JAW_RARLY',true,true);
    }
    
    // ...
   
    // n fragment part
    if (!jaw_get_cache_fragment($template_name, 'n', MONTH_IN_SECONDS)) {
        jaw_start_fragment_caching();

        /// your template code or functions
        
        jaw_set_cache_fragment($template_name, 'n', MONTH_IN_SECONDS);
    }
    
    if(!$cached){
        runkit_constant_redefine("FRAGMENT_CACHING_STATUS", $last_fragment_caching_status);
    }
    
}

```
### Cache cleanup
#### Manual
Admin dashboard -> Tools -> Fragment caching: 
then select Cleanup to delete all fragments
or click delete button near folder or fragment cache file

#### Using functions
```
jaw_cleanup_all_fragments()
```

```
jaw_cleanup_cache_fragments_by_post($postid)
```
* $section  : section to delete, you can use * to delete all sections in post

```
jaw_cleanup_cache_fragments_by_section_refernce($section, $refrence)
```
* $section  : section to delete, you can use * to delete all sections in post
* $refrence : refrence to delete, you can use * to delete all refrences in specific section or all section

```
jaw_cleanup_cache_fragment($postid, $section, $refrence)
```
* $postid   : id of post to delete, you can use * to delete all posts
* $section  : section to delete, you can use * to delete all sections in post
* $refrence : refrence to delete, you can use * to delete all refrences in specific section or all section

#### Using hooks
##### By using add filters: 
```
  jaw_cleanup_all_fragments_paths
                 or
  jaw_cleanup_cache_fragments_by_post_paths

```
for add fragments paths to regular clean up
##### By using add action: 
add cleanup funtion to any wordpress action

## Cleanup cases:
#### Full:

```
       switch_theme
       user_register
       profile_update
       deleted_user
       wp_update_nav_menu
       update_option_sidebars_widgets
       update_option_category_base
       update_option_tag_base
       permalink_structure_changed
       create_term
       edited_terms'
       delete_term
       add_link
       edit_link
       delete_link
       customize_save
       update_option_theme_mods_' . get_option( 'stylesheet' )
       upgrader_process_complete
```
Or when change options, your specific code

#### Partial:
```
      save_post
      edit_post
      delete_post
      wp_trash_post
      clean_post_cache
      wp_update_comment_count
      pre_post_update
```
Or when you add a specific code 

## Hooks:
### Actions:
```

```

### Filters:

```
$cleanup_paths = apply_filters('jaw_cleanup_all_fragments_paths', $cleanup_paths);
$cleanup_paths = apply_filters('jaw_cleanup_cache_fragments_by_post_paths', $cleanup_paths, $postid);
$cleanup_paths = apply_filters('jaw_cleanup_cache_fragments_by_section_refernce_paths', $cleanup_paths, $section, $refrence);
$cleanup_paths = apply_filters('jaw_remove_cache_part_paths', $cleanup_paths, $postid, $section, $refrence);
```

## Parametres:

* Section_name: optional name
* Refernce_in_section : optional name
* Expiration_constant: must be one of plugin Expiration constants or Trensient API constants
* unique_cache: false or true to get 3 types of unique cache "admins, users and visitors" or constant "specific" to sperate by role
* gpdr: false or true to sperate fragment by gpdr

**Note:** those parametres must be the same in jaw_get_cache_part and jaw_set_cache_part for each section and referce

## Expiration constants:

- JAW_RARLY : value controllable form admin dashboard
- JAW_PERSISTANT: set cache to persistant
- JAW_SPECIFIC_1:  value controllable form admin dashboard
- JAW_SPECIFIC_1:  value controllable form admin dashboard
- JAW_SPECIFIC_3:  value controllable form admin dashboard

Or you can use Wordpress Trensient API constants, or duration in seconds 

## Database Table:

* Table name = {$table_prefix}fragment_caching
* Default content:

|  id  |    option_name    |   option_value    | 
| ---- | ----------------- | ----------------- |
|  1   | status            | 1                 |
|  2   | JAW_RARLY         | 1209600           |
|  3   | JAW_SPECIFIC_1    | 43200             |
|  4   | JAW_SPECIFIC_2    | 86400             |
|  5   | JAW_SPECIFIC_3    | 604800            |
|  6   | unique_sufix      | cq0ZvzyfBTTGWbTW  |
|  7   | FRAGMENT_DURATION | 0                 |

## Cache files:

/path/to/your/wp-content/cache/jawc-fragments-caching_{unique_sufix}/{postid}/{section_name}/fragment_cache_ {device} _ {section_name}_ {Refernce_in_section} _ {expiration} _ {user_type} _ {unique_sufix} .php

## Next updates:
- Add more hooks.
- create cache posts and section as folders.
- uplaod fragment cache file
- Preload cache fragments.
- Add gzip.
