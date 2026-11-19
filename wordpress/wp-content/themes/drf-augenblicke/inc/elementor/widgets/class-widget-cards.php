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
                        'card_badge' => 'Gemeinschaft',
                    ],
                    [
                        'card_title' => 'Workshops',
                        'card_desc'  => 'Praxisnah, inspirierend und vielfältig – Workshops für verschiedene Fotografie-Bereiche.',
                        'card_url'   => '/workshops/',
                        'card_image' => ['url' => DRF_THEME_URI . '/assets/images/card-workshops.jpg'],
                        'card_badge' => 'Praxis pur',
                    ],
                    [
                        'card_title' => 'Shuttleservice',
                        'card_desc'  => 'Bequem und unkompliziert – unser Shuttleservice zum Veranstaltungsort.',
                        'card_url'   => '/shuttleservice/',
                        'card_image' => ['url' => DRF_THEME_URI . '/assets/images/card-shuttle.jpg'],
                        'card_badge' => 'Kostenlos',
                    ],
                    [
                        'card_title' => 'Tagungsbüro',
                        'card_desc'  => 'Dein Ansprechpartner vor Ort – für Anmeldung, Infos und alles rund um das Treffen.',
                        'card_url'   => '/tagungsbuero/',
                        'card_image' => ['url' => DRF_THEME_URI . '/assets/images/card-tagungsbuero.jpg'],
                        'card_badge' => 'Orga & Zimmer',
                    ],
                ],
                'title_field' => '{{{ card_title }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $cards = $settings['cards_list'];

        if (empty($cards)) {
            return;
        }
        ?>
        <section class="cards-section drf-elementor-cards" id="drfCardsGrid">
            <div class="container">
                <div class="cards-grid">
                    <?php foreach ($cards as $index => $card) :
                        $img_url = !empty($card['card_image']['url']) ? $card['card_image']['url'] : DRF_THEME_URI . '/assets/images/card-verein.jpg';
                        $url = !empty($card['card_url']) ? $card['card_url'] : '#';
                        ?>
                        <article class="feature-card card-item-<?php echo esc_attr($index + 1); ?>">
                            <a href="<?php echo esc_url($url); ?>" class="card-link-wrapper" aria-label="<?php echo esc_attr($card['card_title']); ?>">
                                <div class="card-media">
                                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($card['card_title']); ?>" loading="lazy">
                                    <div class="card-overlay"></div>
                                    <?php if (!empty($card['card_badge'])) : ?>
                                        <span class="card-badge"><?php echo esc_html($card['card_badge']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h3 class="card-title"><?php echo esc_html($card['card_title']); ?></h3>
                                    <p class="card-desc"><?php echo esc_html($card['card_desc']); ?></p>
                                    <span class="card-arrow" aria-hidden="true">&rarr;</span>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
}
