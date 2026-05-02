<?php
/**
 * Zaymi — рекомендованные плагины (TGM-style без TGM).
 * Показывает в админке список плагинов с прямыми ссылками на установку.
 *
 * @package Zaymi
 */
if (!defined('ABSPATH')) exit;

function zaymi_recommended_plugins(): array {
    return [
        'advanced-custom-fields' => ['name' => 'Advanced Custom Fields', 'note' => '✅ ОБЯЗАТЕЛЬНО — без него не работают поля МФО', 'required' => true],
        'wpcode'                 => ['name' => 'WPCode (Insert Headers and Footers)', 'note' => 'Любые скрипты в head/footer без правки темы'],
        'wp-mail-smtp'           => ['name' => 'WP Mail SMTP', 'note' => 'Чтобы письма заявок реально доходили'],
        'updraftplus'            => ['name' => 'UpdraftPlus', 'note' => 'Авто-бэкапы в Google Drive / Yandex Disk'],
        'litespeed-cache'        => ['name' => 'LiteSpeed Cache', 'note' => 'Скорость + кеш + сжатие картинок'],
        'wordfence'              => ['name' => 'Wordfence Security', 'note' => 'Защита от взлома + 2FA'],
        'cyr2lat'                => ['name' => 'Cyr-To-Lat', 'note' => 'Кириллица → латиница в URL автоматически'],
        'broken-link-checker'    => ['name' => 'Broken Link Checker', 'note' => 'Находит битые ссылки'],
    ];
}

add_action('admin_menu', function () {
    add_submenu_page(
        'zaymi-dashboard',
        'Рекомендуемые плагины',
        '🧩 Плагины',
        'manage_options',
        'zaymi-plugins',
        'zaymi_plugins_page_render'
    );
}, 26);

function zaymi_plugins_page_render() {
    if (!current_user_can('install_plugins')) return;
    if (!function_exists('get_plugins')) require_once ABSPATH.'wp-admin/includes/plugin.php';
    $installed = get_plugins();
    $active_slugs = [];
    foreach ($installed as $file => $p) { $active_slugs[dirname($file)] = is_plugin_active($file) ? 'active' : 'installed'; }
    ?>
    <div class="wrap">
      <h1>🧩 Рекомендуемые плагины</h1>
      <p>Тема Zaymi работает сама по себе, но эти плагины делают её <b>в разы удобнее и безопаснее</b>. Установка одним кликом.</p>

      <table class="widefat striped" style="max-width:1100px">
        <thead>
          <tr><th style="width:30%">Плагин</th><th>Зачем</th><th style="width:160px">Статус</th><th style="width:200px">Действие</th></tr>
        </thead>
        <tbody>
        <?php foreach (zaymi_recommended_plugins() as $slug => $info):
            $st = $active_slugs[$slug] ?? null;
            $install_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin='.$slug), 'install-plugin_'.$slug);
            $search_url  = self_admin_url('plugin-install.php?s='.urlencode($info['name']).'&tab=search&type=term');
        ?>
          <tr>
            <td><b><?php echo esc_html($info['name']); ?></b><?php if (!empty($info['required'])) echo ' <span style="color:#d63638">*</span>'; ?></td>
            <td><?php echo esc_html($info['note']); ?></td>
            <td>
              <?php if ($st === 'active'): ?><span style="color:#00a32a">✅ Активен</span>
              <?php elseif ($st === 'installed'): ?><span style="color:#dba617">⚠️ Установлен (не активен)</span>
              <?php else: ?><span style="color:#999">⬜ Не установлен</span><?php endif; ?>
            </td>
            <td>
              <?php if ($st === 'active'): ?>—
              <?php elseif ($st === 'installed'): ?>
                <a class="button" href="<?php echo esc_url(self_admin_url('plugins.php')); ?>">Включить</a>
              <?php else: ?>
                <a class="button button-primary" href="<?php echo esc_url($install_url); ?>">Установить</a>
                <a class="button" href="<?php echo esc_url($search_url); ?>" target="_blank">Найти</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>

      <p style="margin-top:20px;color:#666"><small>* — обязательный плагин для работы темы.</small></p>
    </div>
    <?php
}
