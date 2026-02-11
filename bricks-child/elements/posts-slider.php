<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Element_Posts_Slider extends \Bricks\Element {
  public $category     = 'custom';
  public $name         = 'posts-slider';
  public $icon         = 'fas fa-images'; 
  public $css_selector = '.posts-slider-wrapper'; 
  public $scripts      = ['bricksSwiper']; 

  public function enqueue_scripts() {
    wp_enqueue_script( 'bricks-swiper' );
    wp_enqueue_style( 'bricks-swiper' );
  }

  public function get_label() {
    return esc_html__( 'Posts Slider', 'bricks' );
  }

  public function set_controls() {
    // CONTENT
    $this->controls['post_type'] = [
        'tab' => 'content',
        'label' => esc_html__( 'Post Type', 'bricks' ),
        'type' => 'select',
        'options' => $this->get_post_type_options(),
        'default' => 'post',
    ];

    $this->controls['posts_per_page'] = [
        'tab' => 'content',
        'label' => esc_html__( 'Limit', 'bricks' ),
        'type' => 'number',
        'default' => 6,
    ];

    $this->controls['taxonomy'] = [
        'tab' => 'content',
        'label' => esc_html__( 'Taxonomy', 'bricks' ),
        'type' => 'select',
        'options' => $this->get_taxonomy_options(),
        'placeholder' => esc_html__( 'Select Taxonomy', 'bricks' ),
    ];

    $this->controls['terms_include'] = [
        'tab' => 'content',
        'label' => esc_html__( 'Include Terms (IDs)', 'bricks' ),
        'type' => 'text',
        'description' => esc_html__( 'Comma-separated term IDs to include.', 'bricks' ),
    ];

    // SLIDER SETTINGS
    $this->controls['slides_per_view'] = [
        'tab' => 'content',
        'label' => esc_html__( 'Slides Per View', 'bricks' ),
        'type' => 'number',
        'default' => 3,
        'min' => 1,
        'max' => 6,
    ];
    
    $this->controls['space_between'] = [
        'tab' => 'content',
        'label' => esc_html__( 'Space Between', 'bricks' ),
        'type' => 'number',
        'default' => 30,
        'units' => true,
    ];
  }

  public function render() {
    $settings = $this->settings;
    
    // Query Args
    $post_type = isset($settings['post_type']) ? $settings['post_type'] : 'post';
    $limit = isset($settings['posts_per_page']) ? $settings['posts_per_page'] : 6;
    $terms = isset($settings['terms_include']) ? explode(',', $settings['terms_include']) : [];
    $taxonomy = isset($settings['taxonomy']) ? $settings['taxonomy'] : 'category';

    $args = [
        'post_type' => $post_type,
        'posts_per_page' => $limit,
        'post_status' => 'publish',
    ];

    // Add tax query if terms are provided
    if ( !empty($terms) && !empty($terms[0]) && $taxonomy ) {
        $clean_terms = array_map('trim', $terms);
        $args['tax_query'] = [
            [
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $clean_terms,
            ]
        ];
    }

    $query = new \WP_Query($args);

    if ( ! $query->have_posts() ) {
        if ( bricks_is_builder_main() ) {
            echo '<div class="posts-slider-empty">No posts found.</div>';
        }
        return;
    }

    // Slider settings
    $slidesPerView = isset($settings['slides_per_view']) ? $settings['slides_per_view'] : 3;
    $spaceBetween = isset($settings['space_between']) ? $settings['space_between'] : 24;
    
    // Unique ID
    $id = 'swiper-' . $this->id;

    echo "<div {$this->render_attributes( '_root' )} class='posts-slider-wrapper'>";
    echo "<div id='{$id}' class='swiper posts-swiper' data-slides-per-view='{$slidesPerView}' data-space-between='{$spaceBetween}'>";
    echo '<div class="swiper-wrapper">';

    while ( $query->have_posts() ) {
        $query->the_post();
        $thumb = get_the_post_thumbnail( get_the_ID(), 'medium_large' );
        $title = get_the_title();
        $link = get_permalink();
        $excerpt = get_the_excerpt();
        
        // New Data
        $author_id = get_the_author_meta('ID');
        $avatar = get_avatar( $author_id, 50 );
        $author_name = get_the_author();
        $date = get_the_date();
        $comments_count = get_comments_number();
        $comments_text = $comments_count === 1 ? '1 comment' : $comments_count . ' comments';

        echo '<div class="swiper-slide">';
            echo '<div class="post-card">';
                if ($thumb) {
                    echo "<div class='post-thumb'><a href='{$link}'>{$thumb}</a></div>";
                }
                echo "<div class='post-content'>";
                    echo "<h3 class='post-title'><a href='{$link}'>{$title}</a></h3>";
                    echo "<div class='post-excerpt'>{$excerpt}</div>";
                    
                    // Meta Section
                    echo "<div class='post-meta'>";
                        echo "<div class='meta-avatar'>{$avatar}</div>";
                        echo "<div class='meta-info'>";
                            echo "<div class='meta-author'>{$author_name}</div>";
                            echo "<div class='meta-date-comments'>{$date} &bull; {$comments_text}</div>";
                        echo "</div>";
                    echo "</div>";

                    // Read More
                    echo "<a href='{$link}' class='post-read-more'>Read more <i class='fas fa-chevron-right'></i></a>";

                echo "</div>";
            echo '</div>';
        echo '</div>';
    }
    wp_reset_postdata();

    echo '</div>'; // swiper-wrapper
    
    // Pagination / Navigation could be added here
    echo '<div class="swiper-pagination"></div>';


    echo '</div>'; // swiper
    echo '</div>'; // wrapper
    
    $this->render_js();
  }
  
  public function render_js() {
      ?>
      <script>
      (function(){
          const id = 'swiper-<?php echo $this->id; ?>';
          const maxRetries = 10;
          let retries = 0;
          
          const initSlider = () => {
              const el = document.getElementById(id);
              if(!el) return;
              
              if(el.swiper) return; // Already initialized

              if(typeof Swiper === 'undefined') {
                  if(retries < maxRetries) {
                      retries++;
                      setTimeout(initSlider, 200);
                  }
                  return;
              }

              const slidesPerView = parseInt(el.getAttribute('data-slides-per-view') || 3);
              const spaceBetween = parseInt(el.getAttribute('data-space-between') || 24);
              
              new Swiper('#' + id, {
                  slidesPerView: 1,
                  spaceBetween: spaceBetween,
                  pagination: {
                      el: '.swiper-pagination',
                      clickable: true,
                  },
                  navigation: {
                      nextEl: '.swiper-button-next',
                      prevEl: '.swiper-button-prev',
                  },
                  breakpoints: {
                      640: {
                          slidesPerView: 1,
                      },
                      768: {
                          slidesPerView: 2,
                      },
                      1024: {
                          slidesPerView: slidesPerView,
                      },
                  }
              });
          };

          if(document.readyState === 'loading') {
              document.addEventListener('DOMContentLoaded', initSlider);
          } else {
              initSlider();
          }
      })();
      </script>
      <?php
  }

  private function get_post_type_options() {
      $post_types = get_post_types(['public' => true], 'objects');
      $options = [];
      foreach($post_types as $pt) {
          $options[$pt->name] = $pt->label;
      }
      return $options;
  }

  private function get_taxonomy_options() {
      $taxonomies = get_taxonomies(['public' => true], 'objects');
      $options = [];
      foreach($taxonomies as $tax) {
          $options[$tax->name] = $tax->label;
      }
      return $options;
  }
}
