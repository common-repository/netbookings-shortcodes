require('pickadate-webpack/lib/picker.date');

(function ($) {

    $.fn.netbookings = function (task, root) {
        //Settings
        let options = {};
        options.breakpoint = 600; //Breakpoint between horizontal/vertical layout (px)
        options.nbBaseURL = nbBaseURL; //Data from localised wordpress else default. If using wordpress plugin, change options from wordpress menu
        options.nbBusinessID = nbBusinessID;
        options.nbPrimaryColour = nbPrimaryColour;
        options.nbSecondaryColour = nbSecondaryColour;
        options.nbSelectedDate = '';
        options.skipped = false; //Only allow to skip a day once

        if (task === 'bathing-availability') {
            if (root === undefined) return;

            options.nbPricingGroup = root.attr('pricinggroup');
            options.nbRoomID = root.attr('roomid');

            //Remove all children
            root.empty();

            //Add wrapper element
            let chart = $("<div class='nb-bathing-availability-wrapper'></div>").appendTo(root);

            //Add date and legend element and attach event handler
            let $div = $([
                "<div class='nb-chart-date'>",
                "   <label for=''> Date",
                "       <input placeholder='Select a date...'>",
                "   </label>",
                "   <div class='nb-chart-legend'>",
                "       <span><span class='colour' style='background: " + nbPrimaryColour + "'></span>Booked</span>",
                "       <span><span class='colour' style='background: " + nbSecondaryColour + "'></span>Available</span>",
                "   </div>",
                "</div>"
            ].join("\n"));
            chart.append($div);

            let horizontal = chart.width() >= options.breakpoint ? true : false;
            if (!horizontal) chart.children('.nb-chart-date').addClass('centered');

            chart.find('input').pickadate({
                weekdaysShort: ['Su', 'M', 'Tu', 'W', 'Th', 'F', 'Sa'],
                selectMonths: 12,
                clear: '',
                min: true,
                onStart: function () {
                    var date = new Date();
                    options.nbSelectedDate = date;
                    this.set('select', date);
                },
                onSet: function (e) {
                    if (e.select) {
                        var date = new Date(e.select);
                        makeAPICall(date.toLocaleDateString('en-AU'), chart, options);
                        options.nbSelectedDate = date;
                    }
                }
            });
        } else if (task === 'init') {
            for (let i = 0; i < this.length; i++) {
                new this.netbookings('bathing-availability', $(this[i]));
            }
        }
    };

    $.fn.attachDragger = function () {
        let attachment = false,
            lastPosition, position, difference
        $($(this).selector).on("mousedown mouseup mousemove", function (e) {
            if (e.type === "mousedown") attachment = true, lastPosition = e.clientX;
            if (e.type === "mouseup") attachment = false;
            if (e.type === "mousemove" && attachment === true) {
                position = e.clientX;
                difference = position - lastPosition;
                $(this).scrollLeft($(this).scrollLeft() - difference);
                lastPosition = e.clientX;
            }
        });
        $(window).on("mouseup", function () {
            attachment = false;
        });
    }

    function formatDate(d) { //Formats into form YYYY-MM-DD
        let month = d.getMonth() + 1;
        let day = d.getDate();

        let output = d.getFullYear() + '-' +
            (month < 10 ? '0' : '') + month + '-' +
            (day < 10 ? '0' : '') + day;

        return output;
    }

    function makeAPICall(date, chart, options) {
        removeExisting(chart);

        //Insert loading animation
        if (chart.children('.nb-lds-ring').length === 0) chart.append('<div class="nb-lds-ring"><div></div><div></div><div></div><div></div></div>');

        //Api call and add elements
        $.ajax({
            url: '/wp-admin/admin-ajax.php?action=nb_get_availability',
            method: 'GET',
            data: {
                date: date,
                room: options.nbRoomID
            },
            success: function (data) {
                try {
                    data = JSON.parse(data);
                } catch (error) {
                    removeExisting(chart);
                    chart.find('.nb-lds-ring').remove();
                    displayError(chart);
                    return;
                }

                let finalStartTime = data.Availability[data.Availability.length - 1].StartTime;
                let finalStartDate = new Date(finalStartTime);

                if (options.skipped == false && finalStartDate < options.nbSelectedDate) {
                    options.nbSelectedDate.setDate(options.nbSelectedDate.getDate() + 1);
                    chart.find('input').pickadate('picker').set('select', options.nbSelectedDate);
                    options.skipped = true;
                } else {
                    createChart(chart, data, options);
                }
            }
        });
    }

    function createChart(chart, data, options) {
        //Remove loading element
        chart.find('.nb-lds-ring').remove();

        if (data.Success === false) {
            displayError(chart);
            return;
        }

        removeExisting(chart);

        let horizontal = chart.width() >= options.breakpoint ? true : false;

        //Add wrapper element
        let classString = 'nb-chart-wrapper ' + (horizontal ? 'horizontal' : 'vertical');
        chart.append('<div class="' + classString + '"></div>');

        const wrapper = chart.find('.nb-chart-wrapper');
        if (horizontal) {
            wrapper.attachDragger();
        }

        let chartData = {
            labels: [],
            bookings: [],
            allowed: [],
            available: []
        };

        for (let i = 0; i < data.Availability.length; i++) {
            chartData.labels.push(convertTime(data.Availability[i].StartTime.substring(11, 16)));
            chartData.bookings.push(data.Availability[i].Bookings);
            chartData.allowed.push(data.Availability[i].Allowed);
            chartData.available.push(data.Availability[i].Available);
        }

        for (let i = 0; i < chartData.labels.length; i++) {
            if (chartData.allowed == 0) continue;

            let proportionFull;
            if (chartData.allowed[i] == 0) {
                proportionFull = 100;
            } else {
                proportionFull = ((chartData.allowed[i] - chartData.available[i]) / chartData.allowed[i]) * 100;
            }
            proportionFull = proportionFull.toString() + '%';

            let time = chartData.labels[i];
            let bookings = chartData.bookings[i];
            let allowed = chartData.allowed[i];
            let available = chartData.available[i];
            let classString = horizontal ? 'nb-chart-column' : 'nb-chart-row';
            let proportionTag = horizontal ? 'height: ' : 'width: ';

            let $row = $([
                "<div class='" + classString + "' style='background: " + nbSecondaryColour + "'>",
                "   <div class='nb-chart-bar' available='" + available + "' bookings='" + bookings + "' allowed='" + allowed + "' style='" + proportionTag + proportionFull + "; background: " + nbPrimaryColour + "'>",
                "       <span>" + time + "</span>",
                "   </div>",
                "</div>"
            ].join("\n"));
            wrapper.append($row);
        }

        let child = $(wrapper.children()[0]);
        if (horizontal && chart.width() < child.outerWidth(true) * chartData.labels.length) { //Tests if chart element is wider than parent (overflows)
            let $row = $([ //Adds stepper controls (arrows)
                "<div class='nb-chart-controls'>",
                "   <div class='back'>",
                "       <svg xmlns='http://www.w3.org/2000/svg' width='45' height='45' viewBox='0 0 640 640' shape-rendering='geometricPrecision' text-rendering='geometricPrecision' image-rendering='optimizeQuality' fill-rule='evenodd' clip-rule='evenodd'>",
                "           <path d='M-.012 320l197.885 196.16V389.898h442.139V250.09H197.873V123.84z'/>",
                "       </svg>",
                "   </div>",
                "   <div class='forward'>",
                "       <svg xmlns='http://www.w3.org/2000/svg' width='45' height='45' viewBox='0 0 640 640' shape-rendering='geometricPrecision' text-rendering='geometricPrecision' image-rendering='optimizeQuality' fill-rule='evenodd' clip-rule='evenodd'>",
                "           <path d='M640.012 320L442.116 516.16V389.898H-.012V250.09h442.128V123.84z'/>",
                "       </svg>",
                "   </div>",
                "</div>"
            ].join("\n"));
            chart.children('.nb-chart-date').append($row);

            chart.find('.nb-chart-controls .back').click(function () { //Click handlers for arrow stepper controls 
                animateScroll(chart.find('.nb-chart-wrapper'), 0);
            });

            chart.find('.nb-chart-controls .forward').click(function () {
                let element = chart.find('.nb-chart-wrapper');
                let scrollLeft = child.outerWidth(true) * chartData.labels.length - element.outerWidth(true);
                animateScroll(element, scrollLeft);
            });

            function animateScroll(element, scrollLeft) {
                element.animate({
                    scrollLeft: scrollLeft
                }, {
                    "duration": 750,
                    "easing": "linear"
                });
            }

            wrapper.addClass('grabbable'); //Makes draggable
            wrapper.on("mousedown touchstart", function (e) {
                $(this).addClass('grabbing')
            })

            wrapper.on("mouseup touchend", function (e) {
                $(this).removeClass('grabbing')
            })
        }

        //Attach click listener to open modal
        $('.nb-chart-column').click(function (event) {
            event.stopPropagation();
            createModal(chart, event.target, options);
        });
        $('.nb-chart-row').click(function (event) {
            event.stopPropagation();
            createModal(chart, event.target, options);
        });

    }

    function displayError(chart) {
        chart.append('<div class="nb-chart-error"><h3>Oops. There was an error fetching the data.</h3></div>');
    }

    function removeExisting(chart) {
        chart.children('.nb-chart-wrapper').remove();
        chart.children('.nb-chart-error').remove();
        chart.find('.nb-chart-date .nb-chart-controls').remove();
    }

    function convertTime(time) {
        time = time.toString().match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];

        if (time.length > 1) {
            time = time.slice(1);
            time[5] = +time[0] < 12 ? 'am' : 'pm';
            time[0] = +time[0] % 12 || 12;
        }
        return time.join('');
    }

    function createModal(chart, target, options) {
        chart.children('.nb-modal').remove();

        //Make sure that target is .nb-chart-bar
        target = $(target);
        if (!target.hasClass('nb-chart-bar')) target = target.children('.nb-chart-bar');

        let top = target.offset().top - chart.offset().top - chart.children('.nb-chart-date').height();
        let available = target.attr('available');
        let disabled = (available == 0) ? 'disabled' : '';
        let time = target.children('span').text();

        let $row = $([
            "<div id='nb-modal' class='nb-modal' style='margin-top: " + top + "px'>",
            "   <div class='nb-modal-header' style='background: " + nbSecondaryColour + "'>",
            "       <span class='close'>&times;</span>",
            "       <h2>" + available + "</h2>",
            "       <h3>Available at " + time + "</h3>",
            "   </div>",
            "   <div class='nb-modal-body'>",
            "       <button type='button' " + disabled + " style='background: " + nbPrimaryColour + "'><i class='fas fa-bookmark'></i>Book Now</button>",
            "   </div>",
            "</div>"
        ].join("\n"));
        chart.append($row);

        $('.nb-modal-header .close').click(function () {
            $('.nb-modal').remove();
        });

        $('.nb-modal-body button').click(function () {
            window.open(options.nbBaseURL + nbBusinessID + '/bookpackage?content=B' + options.nbPricingGroup + '&SD=' + options.nbSelectedDate.toLocaleDateString('en-au').toString(), '_blank');
        })

        $(document).click(function (event) { //Remove modal if click outside of modal
            if (!$(event.target).closest(".nb-modal").length) {
                $('.nb-modal').remove();
            }
        });
    }
})(jQuery);

jQuery('.nb-bathing-availability-chart').netbookings('init');