<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Get all countries stored in database
 * @return array
 */
function get_all_countries()
{
    return hooks()->apply_filters('all_countries', get_instance()->db->order_by('short_name', 'asc')->get(db_prefix().'countries')->result_array());
}
function get_all_areas()
{
    // return hooks()->apply_filters('all_areas', get_instance()->db->order_by('areaid', 'asc')->get(db_prefix().'area')->result_array());
    $CI = & get_instance();
    $CI->db->where('status', 1);
    $CI->db->order_by('name', 'asc');
    $rows = $CI->db->get(db_prefix().'area')->result_array();
    return $rows;
}
function get_all_regions()
{
    return hooks()->apply_filters('all_regions', get_instance()->db->order_by('id', 'asc')->get(db_prefix().'region')->result_array());
}
function get_all_subregions()
{
    return hooks()->apply_filters('all_subregions', get_instance()->db->order_by('id', 'asc')->get(db_prefix().'sub_region')->result_array());
}
function get_all_subjects(){
    return hooks()->apply_filters('all_subjects', get_instance()->db->order_by('id', 'asc')->where('is_active',1)->get(db_prefix().'issue_categories')->result_array());
}
/**
 * Get country row from database based on passed country id
 * @param  mixed $id
 * @return object
 */
function get_country($id)
{
    $CI = & get_instance();

    $country = $CI->app_object_cache->get('db-country-' . $id);

    if (!$country) {
        $CI->db->where('country_id', $id);
        $country = $CI->db->get(db_prefix().'countries')->row();
        $CI->app_object_cache->add('db-country-' . $id, $country);
    }

    return $country;
}
/**
 * Get country short name by passed id
 * @param  mixed $id county id
 * @return mixed
 */
function get_country_short_name($id)
{
    $country = get_country($id);
    if ($country) {
        return $country->iso2;
    }

    return '';
}
/**
 * Get country name by passed id
 * @param  mixed $id county id
 * @return mixed
 */
function get_country_name($id)
{
    $country = get_country($id);
    if ($country) {
        return $country->short_name;
    }

    return '';
}
