<?php
/**
 * Keenshot Widget Areas
 * 
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 * @package Keenshot
 */

function keenshot_widgets_init(){    
    $sidebars = array(
        'sidebar'   => array(
            'name'        => __( 'Sidebar', 'keenshot' ),
            'id'          => 'sidebar', 
            'description' => __( 'Manage your sidebar widgets','keenshot')
        ),
        'instagram'   => array(
            'name'        => __( 'Instagram Feed', 'keenshot' ),
            'id'          => 'instagram', 
            'description' => __( 'Manage your instagram photo', 'keenshot' ),
            'before_widget' => '<div class="widget kd_home_gallery">',
            'after_widget' => '</div>',
            'before_title' => '<div class="widget-title">
            <h3>',
            'after_title'  =>  '</h3>
            <div class="stylish-border"></div>
        </div>',
        ),
        'footer'   => array(
            'name'        => __( 'Footer', 'keenshot' ),
            'id'          => 'footer_widgets', 
            'description' => __( 'Manage your footer widgets', 'keenshot' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<div class="widget-title">
            <h3>',
            'after_title'  =>  '</h3>
            <div class="stylish-border"></div>
        </div>',
        ),
        'woocommerce-sidebar'   => array(
            'name'        => __( 'Woocommerce Sidebar', 'keenshot' ),
            'id'          => 'woocommerce-sidebar', 
            'description' => __( 'Manage your sidebar widgets','keenshot')
        ),
    );

    $i=0;
    foreach( $sidebars as $sidebar ){
        $i++;
        if ( class_exists( 'WooCommerce' ) ) {
            $check = 1;
           
            if($i == 4){
                if($check == 1){
                    register_sidebar( array(
                        'name'          => esc_html( $sidebar['name'] ),
                        'id'            => esc_attr( $sidebar['id'] ),
                        'description'   => esc_html( $sidebar['description'] )
                    ) );
                    break;
                }
            }
        }   
            if($i == 4)  break;

            register_sidebar( array(
                'name'          => esc_html( $sidebar['name'] ),
                'id'            => esc_attr( $sidebar['id'] ),
                'description'   => esc_html( $sidebar['description'] ),
                'before_widget'   => !empty($sidebar['before_widget']) ? $sidebar['before_widget'] : '',
                'after_widget'   => !empty($sidebar['after_widget']) ? $sidebar['after_widget'] : '',
                'before_title'   => !empty($sidebar['before_title']) ? $sidebar['before_title'] : '',
                'after_title'   => !empty($sidebar['after_title']) ? $sidebar['after_title'] : '',
            ) );
        }
    
}
add_action( 'widgets_init', 'keenshot_widgets_init' );

// Adds widget: Keenshot Contact
class Keenshot_contact_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'Keenshot_contact_Widget',
            esc_html__( 'Keenshot Contact', 'keenshot' ),
            array( 'description' => esc_html__( 'Display your contact information', 'keenshot' ), ) // Args
        );
    }

    private $widget_fields = array(
        array(
            'label' => 'Phone',
            'id' => 'phone_text',
            'default' => '(555) 555-1234',
            'type' => 'text',
        ),
        array(
            'label' => 'Email',
            'id' => 'email_email',
            'default' => 'johndoe@gmail.com',
            'type' => 'email',
        ),
        array(
            'label' => 'Address',
            'id' => 'address_textarea',
            'default' => 'Drive Littleton',
            'type' => 'textarea',
        ),
    );

    public function widget( $args, $instance ) {
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        // Output generated fields
        echo '<div class="quick-contact">';
        if(!empty($instance['phone_text'])){
            printf('<div class="info-item">
                <div class="icon">
                    <span class="fa fa-phone"></span>
                </div>
                <a href="tel:%s">%s</a>
            </div>',$instance['phone_text'],$instance['phone_text'] );
        }
        
        if(!empty($instance['email_email'])){
            printf('<div class="info-item">
                <div class="icon">
                    <span class="fa fa-envelope"></span>
                </div>
                <a href="mailto:%s">%s</a>
            </div>',$instance['email_email'],$instance['email_email'] );
        }
        
        if(!empty($instance['address_textarea'])){
            printf('<div class="info-item">
                <div class="icon">
                    <span class="fa fa-map-marker"></span>
                </div>
                <a href="#">%s</a>
            </div>',$instance['address_textarea']);
        }
        echo '</div>';
        
        
        echo $args['after_widget'];
    }

    public function field_generator( $instance ) {
        $output = '';
        foreach ( $this->widget_fields as $widget_field ) {
            $default = '';
            if ( isset($widget_field['default']) ) {
                $default = $widget_field['default'];
            }
            $widget_value = ! empty( $instance[$widget_field['id']] ) ? $instance[$widget_field['id']] : esc_html__( $default, 'keenshot' );
            switch ( $widget_field['type'] ) {
                case 'textarea':
                    $output .= '<p>';
                    $output .= '<label for="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'">'.esc_attr( $widget_field['label'], 'keenshot' ).':</label> ';
                    $output .= '<textarea class="widefat" id="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'" name="'.esc_attr( $this->get_field_name( $widget_field['id'] ) ).'" rows="6" cols="6" value="'.esc_attr( $widget_value ).'">'.$widget_value.'</textarea>';
                    $output .= '</p>';
                    break;
                default:
                    $output .= '<p>';
                    $output .= '<label for="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'">'.esc_attr( $widget_field['label'], 'keenshot' ).':</label> ';
                    $output .= '<input class="widefat" id="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'" name="'.esc_attr( $this->get_field_name( $widget_field['id'] ) ).'" type="'.$widget_field['type'].'" value="'.esc_attr( $widget_value ).'">';
                    $output .= '</p>';
            }
        }
        echo $output;
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'keenshot' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'keenshot' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
        $this->field_generator( $instance );
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        foreach ( $this->widget_fields as $widget_field ) {
            switch ( $widget_field['type'] ) {
                default:
                    $instance[$widget_field['id']] = ( ! empty( $new_instance[$widget_field['id']] ) ) ? strip_tags( $new_instance[$widget_field['id']] ) : '';
            }
        }
        return $instance;
    }
}

function register_Keenshot_contact_Widget() {
    register_widget( 'Keenshot_contact_Widget' );
}
add_action( 'widgets_init', 'register_Keenshot_contact_Widget' );

// Adds widget: Keenshot Copyright
class Keenshot_copyright_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'keenshot_copyright_widget',
            esc_html__( 'Keenshot Copyright', 'keenshot' ),
            array( 'description' => esc_html__( 'Display copy right content', 'keenshot' ), ) // Args
        );
        add_action( 'admin_footer', array( $this, 'media_fields' ) );
        add_action( 'customize_controls_print_footer_scripts', array( $this, 'media_fields' ) );
    }

    private $widget_fields = array(
        array(
            'label' => 'Logo',
            'id' => 'logo_media',
            'type' => 'media',
        ),
        array(
            'label' => 'Content',
            'id' => 'content_textarea',
            'type' => 'textarea',
        ),
        array(
            'label' => 'Copyright text',
            'id' => 'copyrighttext_text',
            'type' => 'text',
        ),
    );

    public function widget( $args, $instance ) {
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        // Output generated fields
        if(!empty($instance['logo_media'] || $instance['content_textarea'] || $instance['copyrighttext_text'])){

        echo '<div class="copy-right">';
        if(!empty($instance['logo_media'])){
            printf('<a href="%s"><img src="%s" class="img-responsive" alt="Keenshot footer image">
</a>', site_url(),wp_get_attachment_url($instance['logo_media']) );
        }
        if(!empty($instance['content_textarea'])){
            echo '<p>'.$instance['content_textarea'].'</p>';
        }
        if(!empty($instance['copyrighttext_text'])){
            echo '<p>'.$instance['copyrighttext_text'].'</p>';
        }
        echo '</div>';
        }
        
        echo $args['after_widget'];
    }

    public function media_fields() {
        ?><script>
            jQuery(document).ready(function($){
                if ( typeof wp.media !== 'undefined' ) {
                    var _custom_media = true,
                    _orig_send_attachment = wp.media.editor.send.attachment;
                    $(document).on('click','.custommedia',function(e) {
                        var send_attachment_bkp = wp.media.editor.send.attachment;
                        var button = $(this);
                        var id = button.attr('id');
                        _custom_media = true;
                            wp.media.editor.send.attachment = function(props, attachment){
                            if ( _custom_media ) {
                                $('input#'+id).val(attachment.id);
                                $('span#preview'+id).css('background-image', 'url('+attachment.url+')');
                                $('input#'+id).trigger('change');
                            } else {
                                return _orig_send_attachment.apply( this, [props, attachment] );
                            };
                        }
                        wp.media.editor.open(button);
                        return false;
                    });
                    $('.add_media').on('click', function(){
                        _custom_media = false;
                    });
                    $(document).on('click', '.remove-media', function() {
                        var parent = $(this).parents('p');
                        parent.find('input[type="media"]').val('').trigger('change');
                        parent.find('span').css('background-image', 'url()');
                    });
                }
            });
        </script><?php
    }

    public function field_generator( $instance ) {
        $output = '';
        foreach ( $this->widget_fields as $widget_field ) {
            $default = '';
            if ( isset($widget_field['default']) ) {
                $default = $widget_field['default'];
            }
            $widget_value = ! empty( $instance[$widget_field['id']] ) ? $instance[$widget_field['id']] : esc_html__( $default, 'keenshot' );
            switch ( $widget_field['type'] ) {
                case 'media':
                    $media_url = '';
                    if ($widget_value) {
                        $media_url = wp_get_attachment_url($widget_value);
                    }
                    $output .= '<p>';
                    $output .= '<label for="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'">'.esc_attr( $widget_field['label'], 'keenshot' ).':</label> ';
                    $output .= '<input style="display:none;" class="widefat" id="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'" name="'.esc_attr( $this->get_field_name( $widget_field['id'] ) ).'" type="'.$widget_field['type'].'" value="'.$widget_value.'">';
                    $output .= '<span id="preview'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'" style="margin-right:10px;border:2px solid #eee;display:block;width: 100px;height:100px;background-image:url('.$media_url.');background-size:contain;background-repeat:no-repeat;"></span>';
                    $output .= '<button id="'.$this->get_field_id( $widget_field['id'] ).'" class="button select-media custommedia">Add Media</button>';
                    $output .= '<input style="width: 19%;" class="button remove-media" id="buttonremove" name="buttonremove" type="button" value="Clear" />';
                    $output .= '</p>';
                    break;
                case 'textarea':
                    $output .= '<p>';
                    $output .= '<label for="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'">'.esc_attr( $widget_field['label'], 'keenshot' ).':</label> ';
                    $output .= '<textarea class="widefat" id="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'" name="'.esc_attr( $this->get_field_name( $widget_field['id'] ) ).'" rows="6" cols="6" value="'.esc_attr( $widget_value ).'">'.$widget_value.'</textarea>';
                    $output .= '</p>';
                    break;
                default:
                    $output .= '<p>';
                    $output .= '<label for="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'">'.esc_attr( $widget_field['label'], 'keenshot' ).':</label> ';
                    $output .= '<input class="widefat" id="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'" name="'.esc_attr( $this->get_field_name( $widget_field['id'] ) ).'" type="'.$widget_field['type'].'" value="'.esc_attr( $widget_value ).'">';
                    $output .= '</p>';
            }
        }
        echo $output;
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'keenshot' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'keenshot' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
        $this->field_generator( $instance );
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        foreach ( $this->widget_fields as $widget_field ) {
            switch ( $widget_field['type'] ) {
                default:
                    $instance[$widget_field['id']] = ( ! empty( $new_instance[$widget_field['id']] ) ) ? strip_tags( $new_instance[$widget_field['id']] ) : '';
            }
        }
        return $instance;
    }
}

function register_keenshot_copyright_widget() {
    register_widget( 'Keenshot_copyright_Widget' );
}
add_action( 'widgets_init', 'register_keenshot_copyright_widget' );