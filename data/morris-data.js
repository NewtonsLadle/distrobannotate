$(function() {
    $.getJSON('/data/stats/kidneycancer.json', function(dat) {
      Morris.Area({
        element: 'morris-area-chart',
        data: dat.totals.progress,
        xkey: 'etime',
        ykeys: ['unannotated_images', 'pending_annotations', 'validated_annotations', 'flagged_annotations'],
        labels: ['Unannotated', 'Pending', 'Validated', 'Flagged'],
        lineColors: ['#337ab7','#f0ad4e','#5cb85c','#d9534f'],
        pointSize: 2,
        hideHover: 'auto',
        resize: true
      });
    });
});
