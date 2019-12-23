# jaw-fragment-caching
## Description:
Simple fragment caching wordpress plugin

## How use it
### Method 1: Direct
```

if (!jaw_get_cache_part('Section_name', 'Refernce_in_section','JAW_SPECIFIC_1')) {
     jaw_start_fragment_caching();
     
     // your code
     
     jaw_set_cache_part('Section_name', 'Refernce_in_section','JAW_SPECIFIC_1');
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
    if (!jaw_get_cache_part($template_name, '1','JAW_RARLY')) {
        jaw_start_fragment_caching();

        /// your template code or functions
        
        jaw_set_cache_part($template_name, '1','JAW_RARLY');
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

Note: those parametres must be the same in jaw_get_cache_part and jaw_set_cache_part for each section and referce

## Expiration constants:

- JAW_RARLY : value controllable form admin dashboard
- JAW_PERSISTANT: set cache to persistant
- JAW_SPECIFIC_1:  value controllable form admin dashboard
- JAW_SPECIFIC_1:  value controllable form admin dashboard
- JAW_SPECIFIC_3:  value controllable form admin dashboard

And you can use Wordpress Trensient API constants

## Next update:
- Control fragments form wordpress dashbord
  * Delete fragments
  * Cleanup all fragment caches
- Delete fragments when update post, menu, widgets ..etc.
