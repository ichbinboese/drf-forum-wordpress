/**
 * DRF Treffen-Anmeldung - Frontend JavaScript
 */
jQuery(document).ready(function ($) {
  var form = $('#drfRegistrationForm');
  if (!form.length) return;

  function formatCurrency(val) {
    return val.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
  }

  // 1. Recalculate Total Amount in Realtime
  function updateCalculation() {
    var roomInput = $('input[name="room_category"]:checked');
    var partInput = $('input[name="participation_type"]:checked');

    var price = parseFloat(roomInput.data('price')) || 0;
    var factor = parseFloat(partInput.data('factor')) || 1;

    var baseAmount = price * factor;

    // Social Donation
    var donationVal = $('input[name="social_donation_select"]:checked').val();
    var donation = 0;
    if (donationVal === 'custom') {
      donation = parseFloat($('#reg_social_donation').val()) || 0;
    } else {
      donation = parseFloat(donationVal) || 0;
      $('#reg_social_donation').val(donation);
    }

    var total = baseAmount + donation;

    $('#drfSummaryBase').text(formatCurrency(baseAmount));
    $('#drfSummaryDonation').text(formatCurrency(donation));
    $('#drfSummaryTotal').text(formatCurrency(total));
  }

  // 2. Room Partner toggle (only if Doppelzimmer)
  $('input[name="room_category"]').on('change', function () {
    if ($(this).val() === 'dz') {
      $('#drfRoomPartnerGroup').slideDown(200);
    } else {
      $('#drfRoomPartnerGroup').slideUp(200);
    }
    updateCalculation();
  });

  // 3. Participation Type change
  $('input[name="participation_type"]').on('change', function () {
    updateCalculation();
  });

  // 4. Shuttle Service toggle
  $('#reg_shuttle_needed').on('change', function () {
    if ($(this).is(':checked')) {
      $('#drfShuttleDetails').slideDown(200);
    } else {
      $('#drfShuttleDetails').slideUp(200);
    }
  });

  // 5. Social Donation pill selection
  $('input[name="social_donation_select"]').on('change', function () {
    if ($(this).val() === 'custom') {
      $('#drfCustomDonationGroup').slideDown(200);
    } else {
      $('#drfCustomDonationGroup').slideUp(200);
      $('#reg_social_donation').val($(this).val());
    }
    updateCalculation();
  });

  $('#reg_social_donation').on('input', function () {
    updateCalculation();
  });

  // Trigger initial calc
  updateCalculation();

  // 6. Form Submission via AJAX
  form.on('submit', function (e) {
    e.preventDefault();

    var feedback = $('#drfFormFeedback');
    var submitBtn = $('#drfSubmitBtn');

    feedback.hide().removeClass('success error');
    submitBtn.prop('disabled', true).addClass('loading').find('span:first').text('Anmeldung wird gesendet...');

    var formData = form.serialize();

    $.ajax({
      url: window.drfAnmeldungData ? window.drfAnmeldungData.ajaxUrl : '/wp-admin/admin-ajax.php',
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function (res) {
        submitBtn.prop('disabled', false).removeClass('loading').find('span:first').text('Verbindlich anmelden');
        if (res.success) {
          feedback.addClass('success').html('<strong>Erfolg!</strong> ' + res.data.message).slideDown(300);
          form[0].reset();
          updateCalculation();
          $('html, body').animate({
            scrollTop: feedback.offset().top - 120
          }, 400);
        } else {
          feedback.addClass('error').html('<strong>Hinweis:</strong> ' + (res.data.message || 'Fehler beim Senden.')).slideDown(300);
        }
      },
      error: function () {
        submitBtn.prop('disabled', false).removeClass('loading').find('span:first').text('Verbindlich anmelden');
        feedback.addClass('error').html('<strong>Fehler:</strong> Server nicht erreichbar. Bitte versuche es später noch einmal.').slideDown(300);
      }
    });
  });
});
