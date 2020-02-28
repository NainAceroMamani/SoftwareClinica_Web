let $doctor, $date, $specialty, $hours;
let $i;

const noHoursalert = ` <div class="alert alert-danger" role="alert">
                    <strong>Lo sentimos!</strong> No se encontraron horas disponibles para el médico en el día seleccionado!
                </div>`;

$(function () {
    $specialty = $('#specialty');
    $doctor = $('#doctor');
    $date = $('#date');
    $hours = $('#hours');

    $specialty.change(() => {
        const specialtyId = $specialty.val();
        const url = `/api/specialties/${ specialtyId }/doctors`;
        $.getJSON(url, onDoctorsLoaded);
    });

    $doctor.change(loadHours);
    $date.change(loadHours);
});

function onDoctorsLoaded(data) {
    // console.log(data);
    let htmlOptions = '';
    data.forEach(doctor => {
        // console.log(`${doctor.id} - ${doctor.name}`)
        htmlOptions += `<option value="${doctor.id}">${doctor.name}</option>`;
    });
    $doctor.html(htmlOptions);
    loadHours();
}

function loadHours() {
    const selectDate = $date.val();
    const doctorId = $doctor.val();
    const url = `/api/schedule/hours?date=${selectDate}&doctor_id=${doctorId}`;
    $.getJSON(url, displayHours);
}

function displayHours(data) {
    // console.log(data);
    if(!data.morning && !data.afternoon) {
        $hours.html(noHoursalert);
        return;
    }

    let htmlHours = '';
    iRadio = 0;

    if(data.morning) {
        const morning_intervals = data.morning;
        morning_intervals.forEach(interval => {
            htmlHours += getRadioIntervalHtml(interval);
        });
    }
    if(data.afternoon) {
        const afternoon_intervals = data.afternoon;
        afternoon_intervals.forEach(interval => {
            htmlHours += getRadioIntervalHtml(interval);
        });
    }
    $hours.html(htmlHours);
}

function getRadioIntervalHtml(interval) {
    const text = `${ interval.start } - ${ interval.end }`;
    return `<div class="custom-control custom-radio mb-3">
                <input type="radio" id="interval${iRadio}" name="scheduled_time" 
                class="custom-control-input" value="${interval.start}" required>
                <label class="custom-control-label" for="interval${iRadio++}">${text}</label>
            </div>`;
}
