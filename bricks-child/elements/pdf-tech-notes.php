<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Element_PDF_Tech_Notes extends \Bricks\Element {
  public $category     = 'custom';
  public $name         = 'pdf-tech-notes';
  public $icon         = 'fas fa-file-pdf'; 
  public $css_selector = '.pdf-tech-notes-wrapper'; 
  public $scripts      = []; 

  public function get_label() {
    return esc_html__( 'PDF Tech Notes', 'bricks' );
  }

  public function set_controls() {
    // CONTENT
    $this->controls['items'] = [
        'tab' => 'content',
        'label' => esc_html__( 'PDF Items', 'bricks' ),
        'type' => 'repeater',
        'titleProperty' => 'title',
        'fields' => [
            'title' => [
                'label' => esc_html__( 'Title', 'bricks' ),
                'type' => 'text',
                'default' => 'New Tech Note',
            ],
            'pdf' => [
                'label' => esc_html__( 'PDF File', 'bricks' ),
                'type' => 'file',
                'library' => 'all', // all, image, video, audio
                'multiple' => false,
                'return' => 'url', // id, url, array
            ],
        ],
        'default' => [
            [
                'title' => 'Sample Tech Note 1',
                'pdf' => '',
            ],
            [
                'title' => 'Sample Tech Note 2',
                'pdf' => '',
            ],
        ],
    ];

    $this->controls['height'] = [
        'tab' => 'style',
        'label' => esc_html__( 'Viewer Height', 'bricks' ),
        'type' => 'text',
        'default' => '600px',
        'description' => esc_html__( 'Height of the PDF viewer (iframe).', 'bricks' ),
    ];
  }

  public function render() {
    $settings = $this->settings;
    
    // Get items
    $items = isset($settings['items']) ? $settings['items'] : [];
    $height = isset($settings['height']) ? $settings['height'] : '600px';

    if ( empty($items) ) {
        if ( bricks_is_builder_main() ) {
            echo '<div class="pdf-tech-notes-empty">No items added. Please add items in the element settings.</div>';
        }
        return;
    }

    $first_pdf = '';
    $list_items = '';
    $count = 0;

    foreach ( $items as $item ) {
        $title = isset($item['title']) ? $item['title'] : '';
        $pdf_url = isset($item['pdf']) ? $item['pdf'] : '';
        
        // Handle array return type just in case
        if ( is_array( $pdf_url ) && isset( $pdf_url['url'] ) ) {
            $pdf_url = $pdf_url['url'];
        } elseif ( is_array( $pdf_url ) ) {
             // Fallback if no URL key found but is array
             $pdf_url = ''; 
        }
        
        // Skip if no PDF
        if ( ! $pdf_url ) continue;

        if ( $count === 0 ) {
            $first_pdf = $pdf_url;
        }

        $active_class = $count === 0 ? 'active' : '';

        $list_items .= "<li class='tech-note-item {$active_class}' data-pdf='{$pdf_url}'>";
        $list_items .= "<span class='tech-note-title'>{$title}</span>";
        $list_items .= "<span class='tech-note-icon'>
        <svg width='8' height='12' viewBox='0 0 8 12' fill='none' xmlns='http://www.w3.org/2000/svg'>
        <path d='M2 12L0 10L4 6L0 2L2 0L8 6L2 12Z' fill='#606060'/>
        </svg>
        </span>"; 
        $list_items .= "</li>";

        $count++;
    }

    if ( empty( $list_items ) ) {
         if ( bricks_is_builder_main() ) {
            echo '<div class="pdf-tech-notes-empty">No valid items found (Items must have a PDF file selected).</div>';
        }
        return;
    }

    // Use set_attribute to add class to _root properly
    $this->set_attribute( '_root', 'class', 'pdf-tech-notes-wrapper' );
    echo "<div {$this->render_attributes( '_root' )}>";
    
    // Left: List
    echo '<div class="tech-note-list-container">';
    echo '<ul class="tech-note-list">';
    echo $list_items;
    echo '</ul>';
    echo '</div>'; // tech-note-list-container

    // Right: Preview
    echo '<div class="tech-note-preview-container">';
    // Append parameters to hide sidebar/toolbar
    $src = $first_pdf . '#toolbar=0&navpanes=0&scrollbar=0';
    echo "<iframe id='tech-note-frame-{$this->id}' class='tech-note-frame' src='{$src}' style='height: {$height};' width='100%'></iframe>";
    echo '</div>'; // tech-note-preview-container

    echo '</div>'; // wrapper
    
    $this->render_js();
  }
  
  public function render_js() {
      ?>
      <script>
      document.addEventListener('DOMContentLoaded', function() {
          const wrapper = document.querySelector('.pdf-tech-notes-wrapper'); 
          // Note: using querySelector might target only the first on page if multiple exist.
          // Better to use closest or ID based if possible, or bind to all items.
          
          const items = document.querySelectorAll('.tech-note-item');
          
          items.forEach(item => {
              item.addEventListener('click', function() {
                  // Find the parent wrapper of this item to scope the iframe search
                  const parentWrapper = this.closest('.pdf-tech-notes-wrapper');
                  if (!parentWrapper) return;

                  const iframe = parentWrapper.querySelector('.tech-note-frame');
                  const pdfUrl = this.getAttribute('data-pdf');

                  if (iframe && pdfUrl) {
                      // Append parameters to hide sidebar/toolbar
                      iframe.src = pdfUrl + '#toolbar=0&navpanes=0&scrollbar=0';
                  }

                  // Update active class
                  const siblings = parentWrapper.querySelectorAll('.tech-note-item');
                  siblings.forEach(sib => sib.classList.remove('active'));
                  this.classList.add('active');
              });
          });
      });
      </script>
      <?php
  }
}
