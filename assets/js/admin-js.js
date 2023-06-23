// console.log('Test');
// console.log(ajax_url);
$(".csv-download").on('click', function (event) {
  event.preventDefault();
  event.stopPropagation();
  event.stopImmediatePropagation();
  var post_id = $(this).attr('id');
  console.log(post_id);
  var delay = 1000;
  $.ajax({
    type: "post",
    url: ajax_url[0],
    data: {
      action: 'user_csv_export',
      post_id: post_id
    },
    error: function (err) {
      console.log(err);
    },
    success: function (response) {
      setTimeout(function () {
        console.log(response);
      }, delay);
    },
  });
});

$('#status-filter').on('change', function (event) {
  var filter_value = $(this).val();
  console.log(filter_value);
  var delay = 1000;
  $.ajax({
    type: "post",
    url: ajax_url[0],
    data: {
      action: 'filter_user_data',
      filter_value: filter_value
    },
    error: function (err) {
      console.log(err);
    },
    success: function (response) {
      setTimeout(function () {
        // console.log(response);
        $('.table-container').children().remove();
        $('.table-container').append(response);
      }, delay);
    },
  });
});