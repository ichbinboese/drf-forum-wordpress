<?php
/**
 * DRF Feature Cards Elementor Widget
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Feature_Cards_Widget extends Widget_Base {

    public function get_name() {
        return 'drf_cards';
    }

    public function get_title() {
        return __('d.r.f 4 Feature Cards Grid', 'drf-augenblicke');
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return ['drf-widgets'];
    }

    public function get_keywords() {
        return ['cards', 'grid', 'features', 'verein', 'workshops', 'shuttle', 'tagungsbuero'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_cards',
            [
                'label' => __('Feature Cards', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'card_title',
            [
                'label'       => __('Titel', 'drf-augenblicke'),
                'type'        => Controls_Manager::TEXT,
                'default'     => 'Card Titel',
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'card_desc',
            [
                'label'       => __('Beschreibung', 'drf-augenblicke'),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => 'Beschreibung der Karte...',
                'rows'        => 3,
            ]
        );

        $repeater->add_control(
            'card_url',
            [
                'label'       => __('Link URL', 'drf-augenblicke'),
                'type'        => Controls_Manager::TEXT,
                'default'     => '#',
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'card_image',
            [
                'label'   => __('Bild / Thumbnail', 'drf-augenblicke'),
                'type'    => Controls_Manager::MEDIA,
                'default' => [
                    'url' => DRF_THEME_URI . '/assets/images/card-verein.jpg',
                ],
            ]
        );

        $repeater->add_control(
            'card_icon',
            [
                'label'   => __('Icon Typ', 'drf-augenblicke'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'users',
                'options' => [
                    'users'    => __('Verein / Gemeinschaft (Personen)', 'drf-augenblicke'),
                    'camera'   => __('Workshops (Kamera)', 'drf-augenblicke'),
                    'bus'      => __('Shuttleservice (Bus / Van)', 'drf-augenblicke'),
                    'calendar' => __('Tagungsbüro (Kalender / Desk)', 'drf-augenblicke'),
                ],
            ]
        );

        $repeater->add_control(
            'card_badge',
            [
                'label'   => __('Badge Text (Optional)', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $this->add_control(
            'cards_list',
            [
                'label'       => __('Karten Liste', 'drf-augenblicke'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    [
                        'card_title' => 'Verein',
                        'card_desc'  => 'Wer wir sind, wofür wir stehen und was uns als Gemeinschaft ausmacht.',
                        'card_url'   => '/verein/',
                        'card_image' => ['url' => DRF_THEME_URI . '/assets/images/card-verein.jpg'],
                        'card_icon'  => 'users',
                        'card_badge' => '',
                    ],
                    [
                        'card_title' => 'Workshops',
                        'card_desc'  => 'Praxisnah, inspirierend und vielfältig – Workshops für verschiedene Fotografie-Bereiche.',
                        'card_url'   => '/workshops/',
                        'card_image' => ['url' => DRF_THEME_URI . '/assets/images/card-workshops.jpg'],
                        'card_icon'  => 'camera',
                        'card_badge' => '',
                    ],
                    [
                        'card_title' => 'Shuttleservice',
                        'card_desc'  => 'Bequem und unkompliziert – unser Shuttleservice zum Veranstaltungsort.',
                        'card_url'   => '/shuttleservice/',
                        'card_image' => ['url' => DRF_THEME_URI . '/assets/images/card-shuttle.jpg'],
                        'card_icon'  => 'bus',
                        'card_badge' => '',
                    ],
                    [
                        'card_title' => 'Tagungsbüro',
                        'card_desc'  => 'Dein Ansprechpartner vor Ort – für Anmeldung, Infos und alles rund um das Treffen.',
                        'card_url'   => '/tagungsbuero/',
                        'card_image' => ['url' => DRF_THEME_URI . '/assets/images/card-tagungsbuero.jpg'],
                        'card_icon'  => 'calendar',
                        'card_badge' => '',
                    ],
                ],
                'title_field' => '{{{ card_title }}}',
            ]
        );

        $this->end_controls_section();
    }

    private function get_card_icon_svg($icon_type) {
        switch ($icon_type) {
            case 'camera':
                return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>';
            case 'bus':
                return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="15" rx="3"></rect><path d="M3 10h18"></path><circle cx="7" cy="15" r="1.5"></circle><circle cx="17" cy="15" r="1.5"></circle><path d="M5 18v2"></path><path d="M19 18v2"></path></svg>';
            case 'calendar':
                return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>';
            case 'users':
            default:
                return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
        }
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $cards = $settings['cards_list'];

        if (empty($cards)) {
            return;
        }
        ?>
        <section class="section-cards drf-elementor-cards" id="drfCardsGrid">
            <div class="container">
                <div class="cards-grid">
                    <?php foreach ($cards as $index => $card) :
                        $img_url = !empty($card['card_image']['url']) ? $card['card_image']['url'] : DRF_THEME_URI . '/assets/images/card-verein.jpg';
                        $url = !empty($card['card_url']) ? $card['card_url'] : '#';
                        $icon_type = !empty($card['card_icon']) ? $card['card_icon'] : 'users';
                        ?>
                        <a href="<?php echo esc_url($url); ?>" class="feature-card card-item-<?php echo esc_attr($index + 1); ?>" aria-label="<?php echo esc_attr($card['card_title']); ?>">
                            <div class="card-media">
                                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($card['card_title']); ?>" loading="lazy">
                                <?php if (!empty($card['card_badge'])) : ?>
                                    <span class="card-badge"><?php echo esc_html($card['card_badge']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <div class="card-header-row">
                                    <div class="card-icon-badge">
                                        <?php echo $this->get_card_icon_svg($icon_type); ?>
                                    </div>
                                    <h3 class="card-title"><?php echo esc_html($card['card_title']); ?></h3>
                                </div>
                                <p class="card-desc"><?php echo esc_html($card['card_desc']); ?></p>
                                <span class="card-arrow" aria-hidden="true">&rarr;</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
}
