/// <reference path="jquery.d.ts" />
var CalendarHelper = (function () {
    function CalendarHelper() {
    }
    CalendarHelper.initTimeCursor = function (time) {
        var _this = this;
        var el = $('#current-time');
        if ($('.calendar-day').length && el.length) {
            var date = new Date(time);
            var diff = time - Date.now();
            if (Math.abs(diff) > 86400000 || date.toDateString() != calendarDayDate.toDateString()) {
                el.hide();
                return;
            }
            var offset = $('.calendar-day thead').outerHeight(true);
            offset += $('.calendar-day .day-events').outerHeight(true);
            offset += (date.getHours() * this.dayCellHeight);
            offset += (date.getMinutes() * (this.dayCellHeight / 60));
            offset += (date.getSeconds() * (this.dayCellHeight / 3600));
            el.show();
            el.css('top', offset);
            $('#current-time > time').html(date.getHours() + ':' + date.getMinutes().leadingZeroes());
            setTimeout(function () {
                _this.initTimeCursor(Date.now() + diff);
            }, 1000);
        }
    };
    CalendarHelper.initContextMenu = function () {
        var _this = this;
        $(document).on('click', function (el) {
            _this.hideContextMenu();
        });
        $('.agenda-item').each(function (index, el) {
            $(el).on('contextmenu', function (e) {
                e.preventDefault();
                _this.selectedItem = _this.clickInsideElement(e, 'agenda-item');
                if (_this.selectedItem) {
                    var id = $(_this.selectedItem).data('id');
                    var approved = $(_this.selectedItem).data('approved');
                    _this.prepareContextMenu(id, approved);
                    _this.showContextMenu();
                    _this.positionContextMenu(e);
                }
                else {
                    _this.selectedItem = null;
                    _this.hideContextMenu();
                }
            });
        });
    };
    // <editor-fold desc="Helper functions">
    CalendarHelper.prepareContextMenu = function (id, approved) {
        $('#context-menu a').each(function (index, el) {
            if ($(el).data('action') == 'approve') {
                if (approved)
                    $(el).hide();
                else
                    $(el).show();
            }
            $(el).attr('href', $(el).data('route').replace('-placeholder-', id));
        });
    };
    CalendarHelper.showContextMenu = function () {
        $('#context-menu').addClass('visible');
    };
    CalendarHelper.hideContextMenu = function () {
        $('#context-menu').removeClass('visible');
    };
    CalendarHelper.positionContextMenu = function (e) {
        var menu = $('#context-menu');
        var clickCoords = this.getPosition(e);
        var clickCoordsX = clickCoords.x;
        var clickCoordsY = clickCoords.y;
        var menuWidth = menu.outerWidth() + 4;
        var menuHeight = menu.outerHeight() + 4;
        var windowWidth = window.innerWidth;
        var windowHeight = window.innerHeight;
        if ((windowWidth - clickCoordsX) < menuWidth) {
            menu.css('left', windowWidth - menuWidth + 'px');
        }
        else {
            menu.css('left', clickCoordsX + 'px');
        }
        if ((windowHeight - clickCoordsY) < menuHeight) {
            menu.css('top', windowHeight - menuHeight + 'px');
        }
        else {
            menu.css('top', clickCoordsY + 'px');
        }
    };
    CalendarHelper.clickInsideElement = function (e, className) {
        var el = e.srcElement || e.target;
        if (el.classList.contains(className)) {
            return el;
        }
        else {
            while (el = el.parentNode) {
                if (el.classList && el.classList.contains(className)) {
                    return el;
                }
            }
        }
        return false;
    };
    CalendarHelper.getPosition = function (e) {
        var posx = 0;
        var posy = 0;
        if (e == undefined)
            e = window.event;
        if (e.pageX || e.pageY) {
            posx = e.pageX;
            posy = e.pageY;
        }
        else if (e.clientX || e.clientY) {
            posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
            posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
        }
        return {
            x: posx,
            y: posy
        };
    };
    CalendarHelper.dayCellHeight = 42;
    CalendarHelper.selectedItem = null;
    return CalendarHelper;
}());
Number.prototype.leadingZeroes = function (size) {
    if (size === void 0) { size = 2; }
    var s = "000000000" + this;
    return s.substr(s.length - size);
};
//# sourceMappingURL=app.js.map