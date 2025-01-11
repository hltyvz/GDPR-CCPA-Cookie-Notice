jQuery(document).ready(function ($) {
    const userLang = navigator.language || navigator.userLanguage; // Tarayıcı dilini al
    const isTurkish = userLang.startsWith('tr');

    const $notice = $('#gdpr-cookie-notice');
    const message = isTurkish ? $notice.data('message-tr') : $notice.data('message-en');
    const policyUrl = isTurkish ? $notice.data('url-tr') : $notice.data('url-en');
    const acceptText = isTurkish ? 'Tamam' : 'Accept';

    // Bildirim içeriğini oluştur
    $notice.html(`
        <p>${message} <a href="${policyUrl}" style="color: ${gdprCookieSettings.text_color}; text-decoration: underline;">${isTurkish ? 'Gizlilik Politikası' : 'Privacy Policy'}</a>.</p>
        <button id="accept-cookies" style="background: ${gdprCookieSettings.button_bg_color}; color: ${gdprCookieSettings.button_text_color};">${acceptText}</button>
    `).css({
        background: gdprCookieSettings.bg_color,
        color: gdprCookieSettings.text_color,
    });

    // Buton hover stilleri
    $('#gdpr-cookie-notice button').hover(function () {
        $(this).css('background', gdprCookieSettings.button_hover_bg_color);
    }, function () {
        $(this).css('background', gdprCookieSettings.button_bg_color);
    });

    // Çerez tercihlerini kaydet
    $('#accept-cookies').on('click', function () {
        document.cookie = `gdpr_cookie_accepted=true; path=/; max-age=${30 * 24 * 60 * 60}`;
        $notice.fadeOut();
    });
});
