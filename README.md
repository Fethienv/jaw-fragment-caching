# jaw-fragment-caching
## Description:
Simple fragment caching wordpress plugin

## Pictures:
![Image of Option](https://github.com/Fethienv/jaw-fragment-caching/blob/master/assets/img/options.PNG?raw=true)
![Image of Fragments](https://github.com/Fethienv/jaw-fragment-caching/blob/master/assets/img/fragments.PNG?raw=true)
![Image of Show Fragments](https://github.com/Fethienv/jaw-fragment-caching/blob/master/assets/img/show%20cache%20files.PNG?raw=true)
![Image of delete Fragments](https://github.com/Fethienv/jaw-fragment-caching/blob/master/assets/img/cache%20files%20deleted.PNG?raw=true)

## How use it
### Method 1: Direct
```

if (!jaw_get_cache_part('Section_name', 'Refernce_in_section','JAW_SPECIFIC_1',true)) {
     jaw_start_fragment_caching();
     
     // your code
     
     jaw_set_cache_part('Section_name', 'Refernce_in_section','JAW_SPECIFIC_1',true);
}

```
### Method 1: Indirect
```

function get_template( $template_path, $template_name, $cached = true ) {
  
    $last_fragment_caching_status = FRAGMENT_CACHING_STATUS;
    if(!$cached){
        runkit_constant_redefine("FRAGMENT_CACHING_STATUS", False);
    }
    
    // first fragment part
    if (!jaw_get_cache_part($template_name, '1','JAW_RARLY',true)) {
        jaw_start_fragment_caching();

        /// your template code or functions
        
        jaw_set_cache_part($template_name, '1','JAW_RARLY',true);
    }
    
    // ...
   
    // n fragment part
    if (!jaw_get_cache_part($template_name, 'n', MONTH_IN_SECONDS)) {
        jaw_start_fragment_caching();

        /// your template code or functions
        
        jaw_set_cache_part($template_name, 'n', MONTH_IN_SECONDS);
    }
    
    if(!$cached){
        runkit_constant_redefine("FRAGMENT_CACHING_STATUS", $last_fragment_caching_status);
    }
    
}

```
## Parametres:

* Section_name: optional name
* Refernce_in_section : optional name
* Expiration_constant: must be one of plugin Expiration constants or Trensient API constants
* unique_cache: true or false to get 3 types of unique cache "admins, users and visitors"

Note: those parametres must be the same in jaw_get_cache_part and jaw_set_cache_part for each section and referce

## Expiration constants:

- JAW_RARLY : value controllable form admin dashboard
- JAW_PERSISTANT: set cache to persistant
- JAW_SPECIFIC_1:  value controllable form admin dashboard
- JAW_SPECIFIC_1:  value controllable form admin dashboard
- JAW_SPECIFIC_3:  value controllable form admin dashboard

And you can use Wordpress Trensient API constants

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

## Next update:
- Control fragments form wordpress dashbord
  * Delete fragments
  * Cleanup all fragment caches
- Delete fragments when update post, menu, widgets ..etc.
- Add gzip.
