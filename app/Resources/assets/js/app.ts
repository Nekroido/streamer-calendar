/// <reference path="jquery.d.ts" />

class CalendarHelper {
    private static dayCellHeight = 42;
    private static selectedItem = null;

    public static initTimeCursor(time:number) {
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

            setTimeout(() => {
                this.initTimeCursor(Date.now() + diff)
            }, 1000);
        }
    }

    public static initContextMenu() {
        $(document).on('click', (el) => {
            this.hideContextMenu();
        });
        $('.agenda-item').each((index, el) => {
            $(el).on('contextmenu', (e) => {
                e.preventDefault();

                this.selectedItem = this.clickInsideElement(e, 'agenda-item');
                if (this.selectedItem) {
                    var id = $(this.selectedItem).data('id');
                    var approved = $(this.selectedItem).data('approved');
                    this.prepareContextMenu(id, approved);
                    this.showContextMenu();
                    this.positionContextMenu(e);
                }
                else {
                    this.selectedItem = null;
                    this.hideContextMenu();
                }
            });
        });
    }

    // <editor-fold desc="Helper functions">
    private static prepareContextMenu(id, approved) {
        $('#context-menu a').each((index, el) => {
            if ($(el).data('action') == 'approve') {
                if (approved)
                    $(el).hide();
                else
                    $(el).show();
            }
            $(el).attr('href', $(el).data('route').replace('-placeholder-', id));
        });
    }

    private static showContextMenu() {
        $('#context-menu').addClass('visible');
    }

    private static hideContextMenu() {
        $('#context-menu').removeClass('visible');
    }

    private static positionContextMenu(e) {
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
        } else {
            menu.css('left', clickCoordsX + 'px');
        }

        if ((windowHeight - clickCoordsY) < menuHeight) {
            menu.css('top', windowHeight - menuHeight + 'px');
        } else {
            menu.css('top', clickCoordsY + 'px');
        }
    }

    private static clickInsideElement(e, className) {
        var el = e.srcElement || e.target;

        if (el.classList.contains(className)) {
            return el;
        } else {
            while (el = el.parentNode) {
                if (el.classList && el.classList.contains(className)) {
                    return el;
                }
            }
        }

        return false;
    }

    private static getPosition(e) {
        var posx = 0;
        var posy = 0;

        if (e == undefined)
            e = window.event;

        if (e.pageX || e.pageY) {
            posx = e.pageX;
            posy = e.pageY;
        } else if (e.clientX || e.clientY) {
            posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
            posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
        }

        return {
            x: posx,
            y: posy
        }
    }

    // </editor-fold>
}

Number.prototype.leadingZeroes = function (size = 2) {
    var s = "000000000" + this;
    return s.substr(s.length - size);
};

declare var calendarDayDate;

interface Number {
    leadingZeroes()
}
declare function bind(thisArg:any, ...argArray:any[]):Function;