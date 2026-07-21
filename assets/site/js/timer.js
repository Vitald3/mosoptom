function sale_timer (self, year, month, day) {
    const deadline = (function(y, m, d) { return new Date(y, m-1, d); })(year, month, day);
    let timerId = null;

    function declensionNum(num, words) {
        return words[(num % 100 > 4 && num % 100 < 20) ? 2 : [2, 0, 1, 1, 1, 2][(num % 10 < 5) ? num % 10 : 5]];
    }

    function countdownTimer() {
        const diff = deadline - new Date();
        if (diff <= 0) {
            clearInterval(timerId);
        }
        const days = diff > 0 ? Math.floor(diff / 1000 / 60 / 60 / 24) : 0;
        const hours = diff > 0 ? Math.floor(diff / 1000 / 60 / 60) % 24 : 0;
        const minutes = diff > 0 ? Math.floor(diff / 1000 / 60) % 60 : 0;
        const seconds = diff > 0 ? Math.floor(diff / 1000) % 60 : 0;
        $days.text(days < 10 ? '0' + days : days);
        $hours.text(hours < 10 ? '0' + hours : hours);
        $minutes.text(minutes < 10 ? '0' + minutes : minutes);
        $seconds.text(seconds < 10 ? '0' + seconds : seconds);
        $days.attr('data-title', declensionNum(days, ['день', 'дня', 'дней']));
        $hours.attr('data-title', declensionNum(hours, ['час', 'часа', 'часов']));
        $minutes.attr('data-title', declensionNum(minutes, ['минута', 'минуты', 'минут']));
        $seconds.attr('data-title', declensionNum(seconds, ['секунда', 'секунды', 'секунд']));
    }

    const $days = $(self).find('.timer__days');
    const $hours = $(self).find('.timer__hours');
    const $minutes = $(self).find('.timer__minutes');
    const $seconds = $(self).find('.timer__seconds');
    countdownTimer();
    timerId = setInterval(countdownTimer, 1000);
}