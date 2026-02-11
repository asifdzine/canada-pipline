<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Element_Custom_Tabs extends \Bricks\Element {
  public $category     = 'custom';
  public $name         = 'custom-tabs';
  public $icon         = 'fas fa-folder'; // Changed icon
  public $css_selector = '.comp-tabs-wrapper'; // Changed selector base

  public function get_label() {
    return esc_html__( 'Advanced Tabs', 'bricks' );
  }

  public function set_controls() {
    // TABS
    $this->controls['tabs_list'] = [
      'tab'     => 'content',
      'label'   => esc_html__( 'Tabs Content', 'bricks' ),
      'type'    => 'repeater',
      'titleProperty' => 'tab_label',
      'fields'  => [
        'tab_label' => [
          'label'   => esc_html__( 'Tab Label', 'bricks' ),
          'type'    => 'text',
          'default' => 'Tab Item',
        ],
        'content_title' => [
          'label' => esc_html__( 'Headline', 'bricks' ),
          'type'  => 'text',
        ],
        'content_image' => [
          'label' => esc_html__( 'Image', 'bricks' ),
          'type'  => 'image',
        ],
        'content_desc' => [
          'label' => esc_html__( 'Description', 'bricks' ),
          'type'  => 'textarea',
        ],
        'cta_text' => [
          'label' => esc_html__( 'Button Label', 'bricks' ),
          'type'  => 'text',
        ],
        'cta_link' => [
          'label' => esc_html__( 'Button URL', 'bricks' ),
          'type'  => 'link',
        ],
      ],
      'default' => [
         ['tab_label' => 'Tab 1', 'content_title' => 'Content 1'],
         ['tab_label' => 'Tab 2', 'content_title' => 'Content 2'],
      ]
    ];

    // EXTRA BUTTONS
    $this->controls['extra_buttons'] = [
      'tab'     => 'content',
      'label'   => esc_html__( 'Sidebar Buttons', 'bricks' ),
      'type'    => 'repeater',
      'titleProperty' => 'label',
      'fields'  => [
        'label' => [ 'label' => 'Label', 'type' => 'text' ],
        'link'  => [ 'label' => 'Link', 'type' => 'link' ],
      ],
      'default' => [
          ['label' => 'Contact Us'],
      ]
    ];
    
    // STYLE - COLOR
    $this->controls['accent_color'] = [
        'tab' => 'style',
        'label' => 'Accent Color',
        'type' => 'color',
        'default' => '#9c27b0',
        'css' => [
            [
                'property' => 'background-color',
                'selector' => '.comp-tab-btn.active, .comp-tab-btn:hover',
            ],
            [
                'property' => 'color',
                'selector' => '.comp-tab-content h2',
            ],
            [
                'property' => 'color',
                'selector' => '.sidebar-btn',
            ],
            [
                'property' => 'border-color',
                'selector' => '.sidebar-btn',
            ]
        ]
    ];
  }

  public function render() {
    $settings = $this->settings;
    $tabs = isset($settings['tabs_list']) ? $settings['tabs_list'] : [];
    $extras = isset($settings['extra_buttons']) ? $settings['extra_buttons'] : [];
    
    if(empty($tabs)) {
        echo '<div class="comp-tabs-empty">Please add tabs.</div>';
        return;
    }

    // Unique ID for this block specifically
    $blockId = 'tabs-' . uniqid();

    echo "<div {$this->render_attributes( '_root' )} id='{$blockId}' data-tabs-container>";
    
    // -- SIDEBAR (LEFT) --
    echo '<div class="comp-tabs-sidebar">';
    
        // Tab Triggers
        echo '<div class="comp-tabs-nav">';
        $i = 0;
        foreach($tabs as $tab) {
            $isActive = ($i === 0) ? 'active' : '';
            $label = isset($tab['tab_label']) ? $tab['tab_label'] : 'Tab';
            
            // data-tab-target matches the index
            echo "<button type='button' class='comp-tab-btn {$isActive}' data-tab-target='{$i}'>{$label}</button>";
            $i++;
        }
        echo '</div>'; // nav

        // Extra Buttons
        if(!empty($extras)) {
            echo '<div class="comp-tabs-extras">';
            foreach($extras as $ex) {
                $lbl = isset($ex['label']) ? $ex['label'] : 'Btn';
                $lnk = isset($ex['link']) ? $ex['link'] : [];
                $url = (is_array($lnk) && isset($lnk['url'])) ? $lnk['url'] : '#';
                echo "<a href='{$url}' class='sidebar-btn'>{$lbl}</a>";
            }
            echo '</div>';
        }

    echo '</div>'; // sidebar

    // -- CONTENT (RIGHT) --
    echo '<div class="comp-tabs-body">';
    $j = 0;
    foreach($tabs as $tab) {
        $isActive = ($j === 0) ? 'active' : '';
        $title = isset($tab['content_title']) ? $tab['content_title'] : '';
        $desc = isset($tab['content_desc']) ? $tab['content_desc'] : '';
        $imgRaw = isset($tab['content_image']) ? $tab['content_image'] : '';
        $ctaTxt = isset($tab['cta_text']) ? $tab['cta_text'] : '';
        $ctaLnk = isset($tab['cta_link']) ? $tab['cta_link'] : [];
        $ctaUrl = (is_array($ctaLnk) && isset($ctaLnk['url'])) ? $ctaLnk['url'] : '#';

        // Image handling
        $imgHtml = '';
        $imgId = (is_array($imgRaw) && isset($imgRaw['id'])) ? $imgRaw['id'] : $imgRaw;
        if($imgId) {
            $imgHtml = wp_get_attachment_image($imgId, 'large');
        }

        echo "<div class='comp-tab-content {$isActive}' data-tab-index='{$j}'>";
            if($title) echo "<h2>{$title}</h2>";
            if($imgHtml) echo "<div class='tab-img'>{$imgHtml}</div>";
            if($desc) echo "<div class='tab-desc'>{$desc}</div>";
            if($ctaTxt) echo "<a href='{$ctaUrl}' class='tab-cta'>{$ctaTxt}</a>";
        echo "</div>";
        $j++;
    }
    echo '</div>'; // body

    echo "</div>"; // root

    $this->render_css();
    $this->render_js();
  }

  public function render_css() {
      ?>
      <style>
      .comp-tabs-wrapper {
          display: flex;
          gap: 30px;
          width: 100%;
      }
      .comp-tabs-sidebar {
          width: 250px;
          flex-shrink: 0;
          display: flex;
          flex-direction: column;
          gap: 30px;
      }
      .comp-tabs-nav {
          display: flex;
          flex-direction: column;
          gap: 10px;
      }
      .comp-tab-btn {
          padding: 12px 16px;
          text-align: center;
          background: #fff;
          border: 1px solid #ddd;
          border-radius: 4px;
          cursor: pointer;
          font-weight: 500;
          transition: all 0.2s;
          color: #333;
      }
      
      .comp-tabs-extras {
          display: flex;
          flex-direction: column;
          gap: 10px;
      }
      .sidebar-btn {
          display: block;
          text-align: center;
          padding: 10px;
          border: 1px solid #9c27b0;
          border-radius: 20px;
          text-decoration: none;
          color: #9c27b0;
          font-size: 0.9em;
      }
      .sidebar-btn:hover {
          background-color: #f5f5f5;
      }

      .comp-tabs-body {
          flex-grow: 1;
      }
      .comp-tab-content {
          display: none;
          animation: compFadeIn 0.4s ease;
      }
      .comp-tab-content.active {
          display: block;
      }
      .comp-tab-content h2 {
          margin-top: 0;
          margin-bottom: 20px;
      }
      .tab-img img {
          max-width: 100%;
          height: auto;
          border-radius: 8px;
          margin-bottom: 20px;
          display: block;
      }
      .tab-desc {
          margin-bottom: 20px;
          line-height: 1.6;
      }
      .tab-cta {
          display: inline-block;
          background: #333;
          color: #fff;
          padding: 10px 24px;
          border-radius: 4px;
          text-decoration: none;
      }

      @keyframes compFadeIn {
          from { opacity: 0; transform: translateY(5px); }
          to { opacity: 1; transform: translateY(0); }
      }

      @media(max-width: 768px) {
          .comp-tabs-wrapper { flex-direction: column; }
          .comp-tabs-sidebar { width: 100%; }
      }
      </style>
      <?php
  }

  public function render_js() {
      // Script renders once but handles all instances via delegation
      ?>
      <script>
      (function(){
        // Prevent multiple bindings if this renders multiple times in builder
        if(window.bricksCustomTabsInit) return;
        window.bricksCustomTabsInit = true;

        document.addEventListener('click', function(e) {
            // Check if clicked element is a tab button
            const btn = e.target.closest('.comp-tab-btn');
            if(!btn) return;

            // Find the container
            const container = btn.closest('[data-tabs-container]');
            if(!container) return;

            e.preventDefault();

            // Get target index
            const targetIdx = btn.getAttribute('data-tab-target');

            // Deactivate all buttons in this container
            const allBtns = container.querySelectorAll('.comp-tab-btn');
            allBtns.forEach(b => b.classList.remove('active'));

            // Deactivate all content in this container
            const allContent = container.querySelectorAll('.comp-tab-content');
            allContent.forEach(c => c.classList.remove('active'));

            // Activate clicked button
            btn.classList.add('active');

            // Activate target content
            const targetContent = container.querySelector(`.comp-tab-content[data-tab-index="${targetIdx}"]`);
            if(targetContent) {
                targetContent.classList.add('active');
            }
        });
      })();
      </script>
      <?php
  }
}
