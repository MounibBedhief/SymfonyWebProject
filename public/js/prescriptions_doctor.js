$(document).ready(function () {
  var table = $('#prescriptionTable').DataTable({
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json',
    },
    order: [[0, 'desc']],
    pageLength: 7,
    dom: 'lrtip',
    columnDefs: [{ orderable: false, targets: 4 }],
  });

  $.fn.dataTable.ext.search.push(function (settings, data) {
    if (settings.nTable.id !== 'prescriptionTable') {
      return true;
    }

    var min = $('#minDate').val();
    var max = $('#maxDate').val();
    var dateCol = data[0] || '';

    if (
      (min === '' && max === '') ||
      (min === '' && dateCol <= max) ||
      (min <= dateCol && max === '') ||
      (min <= dateCol && dateCol <= max)
    ) {
      return true;
    }

    return false;
  });

  $('#btnFilter').on('click', function () {
    table.draw();
  });

  $('#patientSearch').on('keyup change', function () {
    table.column(1).search(this.value).draw();
  });
});
