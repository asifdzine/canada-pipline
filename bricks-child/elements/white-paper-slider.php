<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Element_White_Paper_Slider extends \Bricks\Element {
  public $category     = 'custom';
  public $name         = 'white-paper-slider';
  public $icon         = 'fas fa-file-pdf'; 
  public $css_selector = '.white-paper-slider-wrapper'; 
  public $scripts      = ['bricksSwiper']; 

  public function enqueue_scripts() {
    wp_enqueue_script( 'bricks-swiper' );
    wp_enqueue_style( 'bricks-swiper' );
  }

  public function get_label() {
    return esc_html__( 'White Paper Slider', 'bricks' );
  }

  public function set_controls() {
    // CONTENT
    $this->controls['posts_per_page'] = [
        'tab' => 'content',
        'label' => esc_html__( 'Limit', 'bricks' ),
        'type' => 'number',
        'default' => 6,
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
    $limit = isset($settings['posts_per_page']) ? $settings['posts_per_page'] : 6;

    $args = [
        'post_type' => 'white-paper',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
    ];

    $query = new \WP_Query($args);

    if ( ! $query->have_posts() ) {
        if ( bricks_is_builder_main() ) {
            echo '<div class="white-paper-slider-empty">No white papers found.</div>';
        }
        return;
    }

    // Slider settings
    $slidesPerView = isset($settings['slides_per_view']) ? $settings['slides_per_view'] : 3;
    $spaceBetween = isset($settings['space_between']) ? $settings['space_between'] : 24;
    
    // Unique ID
    $id = 'swiper-' . $this->id;

    echo "<div {$this->render_attributes( '_root' )} class='white-paper-slider-wrapper'>";
    echo "<div id='{$id}' class='swiper white-paper-swiper' data-slides-per-view='{$slidesPerView}' data-space-between='{$spaceBetween}'>";
    echo '<div class="swiper-wrapper">';

    while ( $query->have_posts() ) {
        $query->the_post();
        $thumb = get_the_post_thumbnail( get_the_ID(), 'medium_large' );
        $title = get_the_title();
        $excerpt = get_the_excerpt();
        $pdf_url = get_field('white_paper_pdf'); 
        
        // Fallback or check if empty
        $download_link = $pdf_url ? $pdf_url : '#';
        $download_target = $pdf_url ? '_blank' : '_self';

        echo '<div class="swiper-slide">';
            echo '<div class="white-paper-card">';
                if ($thumb) {
                    echo "<div class='white-paper-thumb'>{$thumb}</div>";
                }
                echo "<div class='white-paper-content'>";
                    echo "<h3 class='white-paper-title'>{$title}</h3>";
                    echo "<div class='white-paper-excerpt'>{$excerpt}</div>";
                    
                    echo "<a href='{$download_link}' target='{$download_target}' class='white-paper-download-btn'>Download Now</a>";

                echo "</div>";
            echo '</div>';
        echo '</div>';
    }
    wp_reset_postdata();

    echo '</div>'; // swiper-wrapper
    
    echo '<div class="swiper-pagination"></div>';
    
    // Navigation arrows
    echo '<div class="swiper-button-prev"></div>';
    echo '<div class="swiper-button-next"></div>';


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
}
