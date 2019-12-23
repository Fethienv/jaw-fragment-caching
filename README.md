# jaw-fragment-caching
Simple fragment caching wordpress plugin

# How use it

if (!jaw_get_cache_part('Section_name', 'Refernce_in_section')) {
                        jaw_start_fragment_caching();
                        // your code
                        jaw_set_cache_part('Section_name', 'Refernce_in_section');
}

# Next update:
- control fragments form wordpress dashbord
  * Add or delete fragment 
  * Set or update fragment expiration time
 
