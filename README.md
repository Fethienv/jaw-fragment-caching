# jaw-fragment-caching
## Description:
Simple fragment caching wordpress plugin

## How use it
### Method 1: Direct
`

if (!jaw_get_cache_part('Section_name', 'Refernce_in_section')) {
     jaw_start_fragment_caching();
     
     // your code
     
     jaw_set_cache_part('Section_name', 'Refernce_in_section');
}

`
### Method 1: Indirect
`

function get_template( $template_path, $template_name, $cached = true ) {
  
    $last_fragment_caching_status = FRAGMENT_CACHING_STATUS;
    if(!$cached){
        runkit_constant_redefine("FRAGMENT_CACHING_STATUS", False);
    }
    
    // first fragment part
    if (!jaw_get_cache_part($template_name, '1')) {
        jaw_start_fragment_caching();

        /// your template code or functions
        
        jaw_set_cache_part($template_name, '1');
    }
    
    // ...
   
    // n fragment part
    if (!jaw_get_cache_part($template_name, 'n')) {
        jaw_start_fragment_caching();

        /// your template code or functions
        
        jaw_set_cache_part($template_name, 'n');
    }
    
    if(!$cached){
        runkit_constant_redefine("FRAGMENT_CACHING_STATUS", $last_fragment_caching_status);
    }
    
}

`
## Next update:
- control fragments form wordpress dashbord
  * Add or delete fragment 
  * Set or update fragment expiration time
 
