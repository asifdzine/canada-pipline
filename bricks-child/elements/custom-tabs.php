<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Element_Custom_Tabs extends \Bricks\Element {
  public $category     = 'custom';
  public $name         = 'custom-tabs';
  public $icon         = 'fas fa-columns'; // Changed icon
  public $css_selector = '.comp-tabs-wrapper'; 
  public $scripts      = ['bricksCustomTabs']; // Register script

  public function get_label() {
    return esc_html__( 'Custom Image Tabs', 'bricks' );
  }

  public function set_controls() {

      $this->controls['sidebar_heading'] = [
        'tab' => 'content',
        'label' => esc_html__( 'Sidebar Title', 'bricks' ),
        'type' => 'text',
        'default' => 'Flow Nozzles & Venturis',
        'placeholder' => 'Enter sidebar Titleng',
    ];

    // TABS REPEATER
    $this->controls['tabs_list'] = [
      'tab'     => 'content',
      'label'   => esc_html__( 'Tabs', 'bricks' ),
      'type'    => 'repeater',
      'titleProperty' => 'tab_label',
      'fields'  => [
        'tab_label' => [
          'label'   => esc_html__( 'Tab Label', 'bricks' ),
          'type'    => 'text',
          'default' => 'Tab Item',
        ],
        // LEFT: Content (Image + Text)
        'content_title' => [
          'label' => esc_html__( 'Headline', 'bricks' ),
          'type'  => 'text',
          'default' => 'Content Title',
        ],
        'content_editor' => [
          'label' => esc_html__( 'Description', 'bricks' ),
          'type'  => 'editor', // Rich Text
          'default' => 'Enter your description here...',
        ],
        
        // RIGHT: Accordion Repeater (Nested)
        'accordion_list' => [
            'label' => esc_html__( 'Accordion Items', 'bricks' ),
            'type' => 'repeater',
            'titleProperty' => 'acc_title',
            'fields' => [
                'acc_title' => [
                    'label' => esc_html__( 'Question / Title', 'bricks' ),
                    'type' => 'text',
                    'default' => 'Accordion Title',
                ],
                'acc_content' => [
                    'label' => esc_html__( 'Answer / Content', 'bricks' ),
                    'type' => 'editor', // Changed to editor for Rich Text support
                    'default' => 'Accordion content goes here.',
                ],
            ],
            'default' => [
                ['acc_title' => 'Feature 1', 'acc_content' => 'Details about feature 1.'],
                ['acc_title' => 'Feature 2', 'acc_content' => 'Details about feature 2.'],
            ]
        ],
      ],
      'default' => [
         ['tab_label' => 'Tab 1', 'content_title' => 'Overview'],
         ['tab_label' => 'Tab 2', 'content_title' => 'Details'],
      ]
    ];
    
    // SIDEBAR SETTINGS
  
    
    // COMPONENT HEADING
    

    // STYLE SETTINGS
    $this->controls['primary_color'] = [
        'tab' => 'style',
        'label' => esc_html__( 'Primary Color', 'bricks' ),
        'type' => 'color',
        'default' => '#f15a29', // Updated default to orange-red from image
        'css' => [
            [
                'property' => 'color',
                'selector' => '.comp-tab-btn.active',
            ],
            [
                'property' => 'color',
                'selector' => '.acc-header.active',
            ],
            [
                'property' => 'color',
                'selector' => '.acc-header:hover',
            ],
        ]
    ];
  }

  public function render() {
    $settings = $this->settings;
    $tabs = isset($settings['tabs_list']) ? $settings['tabs_list'] : [];
    $sidebarHeading = isset($settings['sidebar_heading']) ? $settings['sidebar_heading'] : '';
    $componentHeading = isset($settings['component_heading']) ? $settings['component_heading'] : '';
    
    if(empty($tabs)) {
        if ( bricks_is_builder_main() ) {
            echo '<div class="comp-tabs-empty">Please add tabs in the content settings.</div>';
        }
        return;
    }

    $blockId = 'tabs-' . $this->id;

    echo "<div {$this->render_attributes( '_root' )} id='{$blockId}' class='comp-tabs-wrapper' data-tabs-container>";
    
    if ($componentHeading) {
        echo "<h2 class='comp-tabs-heading'>{$componentHeading}</h2>";
    }
    
    // 1. Sidebar (Tabs)
     echo '<div class="tabs-wrapper">';
    echo '<div class="tabs-sidebar-container">';
    echo '<div class="comp-tabs-sidebar">';
    
    if ($sidebarHeading) {
        echo "<h3 class='tabs-sidebar-heading'>{$sidebarHeading}</h3>";
    }

   echo "<div class='comp-btns-wrapper'>";
	$i = 0;
	foreach($tabs as $tab) {
		$isActive = ($i === 0) ? 'active' : '';
		$label = isset($tab['tab_label']) ? $tab['tab_label'] : 'Tab ' . ($i+1);

		echo "<button type='button' class='comp-tab-btn {$isActive}' data-tab-target='{$i}' role='tab' aria-selected='" . ($isActive ? 'true' : 'false') . "'>";
			echo "<span class='tab-btn-text'>{$label}</span>";
			// Icons
			echo '<span class="tab-icon-active"><img src="https://cpa.tiprojects.ca/wp-content/uploads/2026/02/keyboard_arrow_down-1.png" /></span>';
			echo '<span class="tab-icon-inactive"><img src="https://cpa.tiprojects.ca/wp-content/uploads/2026/02/keyboard_arrow_down.png" /></span>';
		echo "</button>";
		$i++;
	}

	echo "</div>";
    echo '</div>'; 
    echo '</div>'; // tabs-sidebar-container

    // 2. Content Area
    echo '<div class="comp-tabs-body">';
    $j = 0;
    foreach($tabs as $tab) {
        $isActive = ($j === 0) ? 'active' : '';
        $title = isset($tab['content_title']) ? $tab['content_title'] : '';
        $editor = isset($tab['content_editor']) ? $tab['content_editor'] : '';
        $accordions = isset($tab['accordion_list']) ? $tab['accordion_list'] : [];

        echo "<div class='comp-tab-content {$isActive}' data-tab-index='{$j}' role='tabpanel'>";
            
            // Top Section: Image + Text
            echo '<div class="tab-content-grid">';
                // Left: Text
                echo '<div class="tab-text-col">';
                    if($title) echo "<h2 class='tab-title'>{$title}</h2>";
                    if($editor) echo "<div class='tab-editor'>{$editor}</div>";
                echo '</div>';
            echo '</div>';

            // Bottom Section: Accordion
            if(!empty($accordions)) {
                echo '<div class="tab-accordion-wrapper">';
                foreach($accordions as $acc) {
                    $accTitle = isset($acc['acc_title']) ? $acc['acc_title'] : 'Accordion Title';
                    $accContent = isset($acc['acc_content']) ? $acc['acc_content'] : '';
                    
                    echo '<div class="acc-item">';
                        echo '<button class="acc-header" type="button">';
                            echo '<span class="acc-title-text">' . esc_html($accTitle) . '</span>';
                            echo '<span class="acc-icon"><img src="https://cpa.tiprojects.ca/wp-content/uploads/2026/02/keyboard_arrow_down-2.png"/></span>';
                        echo '</button>';
                        echo '<div class="acc-body">';
                            echo '<div class="acc-inner">';
                                echo '<div class="acc-text">' . $accContent . '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
            }

        echo "</div>";
        $j++;
    }
    echo '</div>'; // body

    echo "</div>"; // root
    echo "</div>"; // root

    $this->render_css();
    $this->render_js();
  }

  public function render_css() {
      ?>
      <style>
      /* Component Heading */
      .comp-tabs-heading {
          width: 100%;
          font-size: 32px;
          margin-bottom: 30px;
          color: #333;
          /* text-align: center; Optional, stick to left or check design */
      }
      
      /* Heading Style */
      .tabs-sidebar-heading {
          font-size: 36px;
          font-weight: 700;
          color: #16567B;
          margin-bottom: 16px;
          line-height: 44px;
      }
      
      .comp-tab-btn {
          display: flex;
          justify-content: space-between;
          align-items: center;
          text-align: left;
          padding: 24px;
          background: transparent;
          border: none;
          border-bottom: 1px solid #eee;
          cursor: pointer;
          font-weight: 400;
          font-size: 16px;
		  line-height: 24px;
          color: #4A4A4A;
          transition: all 0.2s ease;
          width: 100%;
      }
      
      .comp-tab-btn:hover {
          color: #ED4023;
          background: transparent;
      }
      
      .comp-tab-btn.active {
          background: transparent;
          color: #ED4023;
          font-weight: 600;
          box-shadow: none;
          border-left: none;
      }
      
      .tab-icon-active, .tab-icon-inactive {
          font-size: 12px;
      }
      
      .comp-tab-btn.active .tab-icon-inactive { display: none; }
      .comp-tab-btn:not(.active) .tab-icon-active { display: none; }

      /* Retain other existing styles if any, but since we are overriding global css, ensure specificity */
      </style>
      <?php
  }

  public function render_js() {
      ?>
      <script>
      (function() {
          if (window.bricksCustomTabsInit) return;
          window.bricksCustomTabsInit = true;

          document.addEventListener('click', function(e) {
              
              // --- TABS LOGIC ---
              const tabBtn = e.target.closest('.comp-tab-btn');
              if (tabBtn) {
                  const container = tabBtn.closest('[data-tabs-container]');
                  if (container) {
                      e.preventDefault();
                      
                      // Remove active class from all buttons and content in THIS container
                      container.querySelectorAll('.comp-tab-btn').forEach(b => b.classList.remove('active'));
                      container.querySelectorAll('.comp-tab-content').forEach(c => c.classList.remove('active'));
                      
                      // Activate clicked button
                      tabBtn.classList.add('active');
                      
                      // Activate target content
                      const idx = tabBtn.getAttribute('data-tab-target');
                      const target = container.querySelector(`.comp-tab-content[data-tab-index="${idx}"]`);
                      if (target) target.classList.add('active');
                  }
              }

              // --- ACCORDION LOGIC ---
              const accHeader = e.target.closest('.acc-header');
              if (accHeader) {
                  e.preventDefault();
                  const accItem = accHeader.closest('.acc-item');
                  const accBody = accItem.querySelector('.acc-body');
                  
                  // Toggle Active Class
                  const isOpen = accHeader.classList.contains('active');
                  
                  // Optional: Close other accordions in the same group? 
                  // Usually accordions inside a section behave independently or as an accordion group.
                  // Let's implement independent behavior for simplicity unless requested otherwise.
                  
                  if (isOpen) {
                      accHeader.classList.remove('active');
                      accBody.style.maxHeight = null;
                  } else {
                      accHeader.classList.add('active');
                      accBody.style.maxHeight = accBody.scrollHeight + "px";
                  }
              }
          });
      })();
      </script>
      <?php
  }
}
