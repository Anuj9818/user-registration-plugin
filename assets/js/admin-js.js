// console.log('Test');
// console.log(ajax_url);
$(".csv-download").on('click', function (event) {
  event.preventDefault();
  event.stopPropagation();
  event.stopImmediatePropagation();
  var post_id = $(this).attr('id');
  // console.log(post_id);
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
        var a = document.createElement('a');
        var binaryData = [];
        binaryData.push(response);
        var url = window.URL.createObjectURL(new Blob(binaryData, { type: "application/zip" }))
        const date = new Date();
        let timestamp = date.getTime();
        a.href = url;
        a.download = post_id + '-' + timestamp + '.csv';
        document.body.append(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
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

function downloadCSV(Id, event) {
  event.preventDefault();
  var post_id = Id;
  // console.log(post_id);
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
        var a = document.createElement('a');
        var binaryData = [];
        binaryData.push(response);
        var url = window.URL.createObjectURL(new Blob(binaryData, { type: "application/zip" }))
        const date = new Date();
        let timestamp = date.getTime();
        a.href = url;
        a.download = post_id + '-' + timestamp + '.csv';
        document.body.append(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
      }, delay);
    },
  });
}