<?php
class unitModel{
    public $unit_data = array(
        'unit_number'   => '',
        'element'       => '',
        'rarity'        => '',
        'max_level'     => '',
        'cost'          => '',
        'hit_count'     => '',
        'bb_hits'       => '',
        'sbb_hits'      => '',
        'bb_fill'       => '',
        'sbb_fill'      => '',
        'lord_hp'       => '',
        'lord_atk'      => '',
        'lord_def'      => '',
        'lord_rec'      => '',
        'anima_hp'      => '',
        'anima_atk'     => '',
        'anima_def'     => '',
        'anima_rec'     => '',
        'breaker_hp'    => '',
        'breaker_atk'   => '',
        'breaker_def'   => '',
        'breaker_rec'   => '',
        'guardian_hp'   => '',
        'guardian_atk'  => '',
        'guardian_def'  => '',
        'guardian_rec'  => '',
        'oracle_hp'     => '',
        'oracle_atk'    => '',
        'oracle_def'    => '',
        'oracle_rec'    => '',
        'max_hp_bonus'  => '',
        'max_atk_bonus' => '',
        'max_def_bonus' => '',
        'max_rec_bonus' => '',
        'leader_skill' => '',
        'brave_burst'   => '',
        'super_brave_burst'     => '',
        'leaders_skill_effect'  => '',
        'brave_burst_effect'    => '',
        'super_brave_burst_effect'  => ''
    );

    public $wp_post = array(
        'title'         => '',
        'description'   => '',
        'thumbnail'     => ''
    );

    public $field_keys = array(
        'unit_number'   => 'field_5499964631ee0',
        'element'       => 'field_5499968931ee1',
        'rarity'        => 'field_549996d831ee2',
        'max_level'     => 'field_549996f831ee3',
        'cost'          => 'field_5499973031ee4',
        'hit_count'     => 'field_5499973e31ee5',
        'bb_hits'       => 'field_549da1cf605cc',
        'sbb_hits'      => 'field_549da1e5605cd',
        'bb_fill'       => 'field_5499975331ee6',
        'sbb_fill'      => 'field_5499977131ee7',
        'lord_hp'       => 'field_549997c631ee8',
        'lord_atk'      => 'field_549997e031ee9',
        'lord_def'      => 'field_549997fd31eea',
        'lord_rec'      => 'field_5499980f31eeb',
        'anima_hp'      => 'field_5499983e31eec',
        'anima_atk'     => 'field_5499985531eed',
        'anima_def'     => 'field_5499986a31eee',
        'anima_rec'     => 'field_5499987f31eef',
        'breaker_hp'    => 'field_5499989c31ef0',
        'breaker_atk'   => 'field_549998aa31ef1',
        'breaker_def'   => 'field_549998bd31ef2',
        'breaker_rec'   => 'field_549998cf31ef3',
        'guardian_hp'   => 'field_549998ff99dc3',
        'guardian_atk'  => 'field_5499990e99dc4',
        'guardian_def'  => 'field_5499992399dc5',
        'guardian_rec'  => 'field_5499993299dc6',
        'oracle_hp'     => 'field_5499994399dc7',
        'oracle_atk'    => 'field_5499995699dc8',
        'oracle_def'    => 'field_5499996699dc9',
        'oracle_rec'    => 'field_5499997399dca',
        'max_hp_bonus'  => 'field_5499999399dcb',
        'max_atk_bonus' => 'field_549999a999dcc',
        'max_def_bonus' => 'field_549999bc99dcd',
        'max_rec_bonus' => 'field_549999cc99dce',
        'leader_skill'  => 'field_549999e3f6635',
        'brave_burst'   => 'field_54999a71f6637',
        'super_brave_burst'     => 'field_54999aaef6639',
        'leaders_skill_effect'  => 'field_54999a5df6636',
        'brave_burst_effect'    => 'field_54999a81f6638',
        'super_brave_burst_effect'  => 'field_54999abdf663a'
    );

    private function update_featured_image($url, $post_id){
        if(trim((string)$url) != '' && !has_post_thumbnail($post_id) ){
            $tmp = download_url( (string)$url );
            $file_array = array();

            // Set variables for storage
            // fix file filename for query strings
            preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', (string)$url, $matches);
            $file_array['name'] = basename($matches[0]);
            $file_array['tmp_name'] = $tmp;

            // If error storing temporarily, unlink
            if ( is_wp_error( $tmp ) ) {
                @unlink($file_array['tmp_name']);
                $file_array['tmp_name'] = '';
            }

            // do the validation and storage stuff
            $attachment_id = media_handle_sideload( $file_array, $post_id );

            if ( is_wp_error( $attachment_id ) ) {
                array_push($error_messages, "We were unable to add the image specified to the unit.");
            } else {
                set_post_thumbnail($post_id, $attachment_id);
            }
        }
    }

    public function create_update_unit($data){
    
        $error_messages = array();

        if($data['title'] == "" || $data['description'] == "" || 
            $data['title'] == " " || $data['description'] == " " || 
            empty($data['title']) || empty($data['description']) ){
            array_push($error_messages, "You must fill in all form fields. Missing: Title or Description");
        }
        foreach($this->unit_data as $field => $value){
            if(trim((string)$data[$field]) == "" || (string)$data[$field] == " " || empty($data[$field]) ){
                array_push($error_messages, "All data is required. Missing: " . $field);
            }
        }

        //Check for existing unit by unit_number
        $args = array(
            'post_type'     => 'unit',
            'meta_key'      => 'unit_number',
            'meta_value'    => intval($data['unit_number']),
        );
        $units = new WP_Query( $args );
        if( $units->have_posts() && count($error_messages) === 0 ) : 
            while( $units->have_posts() ) : $units->the_post();
                if ($units->found_posts == 1){
                    if ( current_user_can('edit_posts') ){
                        //Update the existing post data
                        $post_id = get_the_ID();
                        $wp_post = array(
                            'post_id'       => $post_id,
                            'post_title'    => sanitize_text_field($data['title']),
                            'post_content'  => wp_kses_post($data['description'])
                        );

                        foreach($this->unit_data as $field => $value){
                            update_field($this->field_keys[$field], $data[$field], $post_id);
                        }

                        wp_update_post( $wp_post);

                        //Only add a post thumbnail if these isn't one already.
                        if(isset($data['icon'])){
                            $this->update_featured_image($data['icon'], $post_id);
                        }

                        $this->unit_data['success'] = "Unit data updated!  Thank you.";
                        array_merge($this->unit_data, $data);
                        return $this->unit_data;
                    }else{
                        array_push($error_messages, "You do not have permission to update existing units please sign up or login.");
                    }
                }else{
                    array_push($error_messages, "DANGER WILL ROBINSON THERE ARE MORE THAN ONE OF THIS UNIT!!!! DOES NOT COMPUTE... ABORT! ABORT!");
                }
            endwhile;
        endif;
        if (count($error_messages) === 0){
            $new_post = array(
                'post_title'   => sanitize_text_field($data['title']),
                'post_content' => wp_kses_post($data['description']),
                'post_status'  => 'publish',
                'post_type'    => 'unit',
                'post_date'    => date('Y-m-d H:i:s')
            );
            // create!
            $id = wp_insert_post($new_post);

            if(isset($data['icon'])){
                $this->update_featured_image($data['icon'], $id);
            }
            
            //Now that we have a new post ID update the associated field data
            foreach($this->unit_data as $field => $value){
                update_field($this->field_keys[$field], $data[$field], $id);
            }

            $this->unit_data['success'] = "Unit Submitted!  Once the unit data is reviewed by an admin it will be published to the list. Thank you.";
            array_merge($this->unit_data, $data);
            return $this->unit_data;
        }else{
            $this->unit_data['errors'] = $error_messages;
            array_merge($this->unit_data, $data);
            return $this->unit_data;
        }
    }
}