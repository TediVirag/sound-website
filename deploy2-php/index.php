<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n="pageTitle">Emotion Rating Questionnaire</title>
    <link rel="stylesheet" href="/static/style.css">
    <link rel="icon" type="image/png" sizes="512x512" href="/static/favicon.png">
</head>

<body>
    <div class="container">
        
        <!-- Language Switcher -->
        <div class="lang-switcher">
            <button id="btn-en" class="active" onclick="setLanguage('en')">English</button>
            <button id="btn-hu" onclick="setLanguage('hu')">Magyar</button>
        </div>

        <div class="privacy-policy" id="privacyPolicy">
            <h2 data-i18n="privacyTitle">Privacy Policy</h2>
            <div data-i18n="privacyText">
                <p>You are about to participate in research, coordinated by <strong>Dr. Balázs Nagy</strong> (e-mail:
                    <strong>nagybalazs@inf.elte.hu</strong>). The research is carried out by highly qualified professionals
                    and their assistants. The aim of this study is to collect auditory perception data for sound
                    classification and evaluation research to help the development of audio processing applications.
                </p>
                <p><strong>Participation is voluntary.</strong> Listening to the audio samples and filling out the
                    questionnaires is harmless and without any foreseen risks. It is possible to pause the study at any time
                    to avoid fatigue. Participants can also withdraw consent and terminate participation at any time without
                    any reason and without any consequences. <strong>Monetary compensation is not due for
                        participation.</strong></p>
                <p>During the study the participant will be requested to listen to <strong>5-10 different sound
                        samples</strong> through their audio device and provide evaluations according to the online form
                    instructions, which will last for about <strong>10-15 minutes.</strong></p>
                <p>The results of this study will later be used in <strong>publications</strong> and will also be presented
                    at <strong>scientific conferences.</strong> If requested, written or verbal information will be provided
                    on these events.</p>
                <p>All information (including video and/or audio material, if it was part of the research) collected during
                    this research will be handled with <strong>strict confidentiality.</strong> Data obtained during the
                    research is stored as coded information on a secure computer and paper-based material (e.g.
                    questionnaires) is kept in a safe or a locked office also in a coded format. The individual codes are
                    provided by the assistant in charge, and these are accessible and known only to her/him. Data of the
                    research are analyzed statistically during which <strong>no personal identification is
                        possible.</strong> The document with the rules regulating personal data processing (<strong>General
                        Data Protection Regulation, GDPR</strong>) is attached with its enclosures.</p>
                <p><strong>No medical or laboratory report</strong> will be prepared about the results of the study. Verbal
                    account can be provided about the findings upon request.</p> 
            </div>
            <p data-i18n="privacyCheckText">Please read the following and check all boxes to proceed:</p>
            <ul>
                <li><label><input type="checkbox" class="privacy-checkbox"> <span data-i18n="priv1">I have read and understood the information regarding my participation in this online research study</span></label></li>
                <li><label><input type="checkbox" class="privacy-checkbox"> <span data-i18n="priv2">I agree to the conditions and consent to participate in the study</span></label></li>
                <li><label><input type="checkbox" class="privacy-checkbox"> <span data-i18n="priv3">I give my consent for the anonymous data collected to be used for research purposes and made accessible to other researchers</span></label></li>
                <li><label><input type="checkbox" class="privacy-checkbox"> <span data-i18n="priv4">I understand I can terminate my participation at any time, and my data will be erased upon request</span></label></li>
                <li><label><input type="checkbox" class="privacy-checkbox"> <span data-i18n="priv5">I understand that ELTE FoI Department of Artificial Intelligence will handle my personal data confidentially and will not allow access to unauthorized parties</span></label></li>
                <li><label><input type="checkbox" class="privacy-checkbox"> <span data-i18n="priv6">I have read and agree to the Privacy Notice</span></label></li>
                <li><label><input type="checkbox" id="check-all" class="privacy-checkbox"> <span data-i18n="privAll">I agree to all the terms and conditions outlined above</span></label></li>
            </ul>
            <button id="agreeButton" data-i18n="btnAgree" disabled>I Agree</button>
        </div>

        <div class="questionnaire-box">
            <h1 data-i18n="mainTitle">Emotion Rating Questionnaire</h1>
            <p class="subtitle" data-i18n="subTitle">Please complete the following questionnaire</p>

            <form id="questionnaireForm" novalidate>
                <!-- Demographics Section -->
                <div class="section">
                    <h2 data-i18n="demoTitle">Demographics</h2>

                    <div class="form-group" id="age-group">
                        <label for="age" data-i18n="lblAge">Age *</label>
                        <input type="number" id="age" name="age" min="1" max="120" required>
                        <div class="error-message" data-i18n="errAge">Please enter your age.</div>
                    </div>

                    <div class="form-group" id="gender-group">
                        <label for="gender" data-i18n="lblGender">Gender *</label>
                        <select id="gender" name="gender" required>
                            <option value="" data-i18n="optSelect">-- Select --</option>
                            <option value="Male" data-i18n="optMale">Male</option>
                            <option value="Female" data-i18n="optFemale">Female</option>
                            <option value="Non-binary" data-i18n="optNonBinary">Non-binary</option>
                            <option value="Other" data-i18n="optOther">Other</option>
                        </select>
                        <div class="error-message" data-i18n="errGender">Please select your gender.</div>
                    </div>

                    <div class="form-group" id="education-group">
                        <label for="highest_education" data-i18n="lblEdu">Highest Education *</label>
                        <select id="highest_education" name="highest_education" required>
                            <option value="" data-i18n="optSelect">-- Select --</option>
                            <option value="Less than high school" data-i18n="optEdu1">Less than high school</option>
                            <option value="High school" data-i18n="optEdu2">High school</option>
                            <option value="Some college" data-i18n="optEdu3">Some college</option>
                            <option value="Bachelor's degree" data-i18n="optEdu4">Bachelor's degree</option>
                            <option value="Master's degree" data-i18n="optEdu5">Master's degree</option>
                            <option value="Doctoral degree" data-i18n="optEdu6">Doctoral degree</option>
                        </select>
                        <div class="error-message" data-i18n="errEdu">Please select your highest education level.</div>
                    </div>

                    <div class="form-group" id="submitted-before-group">
                        <label data-i18n="lblBefore">Have you completed this questionnaire before? *</label>
                        <div class="radio-group">
                            <label><input type="radio" name="submitted_before" value="true" required> <span data-i18n="optYes">Yes</span></label>
                            <label><input type="radio" name="submitted_before" value="false"> <span data-i18n="optNo">No</span></label>
                        </div>
                        <div class="error-message" data-i18n="errBefore">Please select an option.</div>
                    </div>
                </div>

                <!-- Sound Selection Section -->
                <div class="section" id="soundSelectionSection">
                    <h2 data-i18n="soundTitle">Sound Rating</h2>
                    <p class="info-text" data-i18n="soundInfo">How many sounds would you like to rate?</p>

                    <div class="form-group">
                        <label for="numSounds" data-i18n="lblNumSounds">Number of Sounds *</label>
                        <select id="numSounds" name="numSounds">
                            <option value="1">1</option>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                        </select>
                    </div>

                    <button type="button" class="submit-btn" onclick="loadSounds()" data-i18n="btnLoad">Load Sounds</button>
                </div>

                <!-- Emotion Ratings Section -->
                <div class="section" id="emotionRatingsSection" style="display: none;">
                    <h2 data-i18n="ratingTitle">Emotion Ratings</h2>
                    <p class="info-text" data-i18n="ratingInfo">Listen to each sound and rate the emotions you feel. You can select 1 or 2 emotions per sound.</p>
                    <div id="emotionRatings"></div>
                </div>

                <!-- Feedback Section -->
                <div class="section" id="feedbackSection" style="display: none;">
                    <h2 data-i18n="feedbackTitle">Additional Feedback</h2>
                    <div class="form-group">
                        <label for="feedback" data-i18n="lblFeedback">Comments (Optional)</label>
                        <textarea id="feedback" name="feedback" rows="4"></textarea>
                    </div>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn" style="display: none;" data-i18n="btnSubmit">Submit Questionnaire</button>
            </form>

            <div id="message" class="message"></div>
        </div>
    </div>

    <!-- Modal for Success Message -->
    <div class="modal-backdrop" id="modalBackdrop"></div>
    <div class="modal-success" id="modalSuccess">
        <div class="success-icon">&#x2714;</div>
        <div class="modal-message" id="modalMessage"></div>
        <div class="modal-code" id="modalCode"></div>
        <div class="modal-info" id="modalInfo" style="font-size:.97em;color:#333;margin-bottom:8px;" data-i18n="modalSave">
            Make sure to save this code — you'll need it if you want your data deleted.
        </div>
        <button id="fillAgainBtn" type="button" data-i18n="btnAnother">Fill Another Questionnaire</button>
    </div>

    <script>
        // --- TRANSLATION DICTIONARY ---
        const translations = {
            en: {
                pageTitle: "Emotion Rating Questionnaire",
                privacyTitle: "Privacy Policy",
                privacyText: `
                    <p>You are about to participate in research, coordinated by <strong>Dr. Balázs Nagy</strong> (e-mail:
                        <strong>nagybalazs@inf.elte.hu</strong>). The research is carried out by highly qualified professionals
                        and their assistants. The aim of this study is to collect auditory perception data for sound
                        classification and evaluation research to help the development of audio processing applications.
                    </p>
                    <p><strong>Participation is voluntary.</strong> Listening to the audio samples and filling out the
                        questionnaires is harmless and without any foreseen risks. It is possible to pause the study at any time
                        to avoid fatigue. Participants can also withdraw consent and terminate participation at any time without
                        any reason and without any consequences. <strong>Monetary compensation is not due for
                            participation.</strong></p>
                    <p>During the study the participant will be requested to listen to <strong>5-10 different sound
                            samples</strong> through their audio device and provide evaluations according to the online form
                        instructions, which will last for about <strong>10-15 minutes.</strong></p>
                    <p>The results of this study will later be used in <strong>publications</strong> and will also be presented
                        at <strong>scientific conferences.</strong> If requested, written or verbal information will be provided
                        on these events.</p>
                    <p>All information (including video and/or audio material, if it was part of the research) collected during
                        this research will be handled with <strong>strict confidentiality.</strong> Data obtained during the
                        research is stored as coded information on a secure computer and paper-based material (e.g.
                        questionnaires) is kept in a safe or a locked office also in a coded format. The individual codes are
                        provided by the assistant in charge, and these are accessible and known only to her/him. Data of the
                        research are analyzed statistically during which <strong>no personal identification is
                            possible.</strong> The document with the rules regulating personal data processing (<strong>General
                            Data Protection Regulation, GDPR</strong>) is attached with its enclosures.</p>
                    <p><strong>No medical or laboratory report</strong> will be prepared about the results of the study. Verbal
                        account can be provided about the findings upon request.</p>`,
                privacyCheckText: "Please read the following and check all boxes to proceed:",
                priv1: "I have read and understood the information regarding my participation in this online research study",
                priv2: "I agree to the conditions and consent to participate in the study",
                priv3: "I give my consent for the anonymous data collected to be used for research purposes and made accessible to other researchers",
                priv4: "I understand I can terminate my participation at any time, and my data will be erased upon request",
                priv5: "I understand that ELTE FoI Department of Artificial Intelligence will handle my personal data confidentially and will not allow access to unauthorized parties",
                priv6: "I have read and agree to the Privacy Notice",
                privAll: "I agree to all the terms and conditions outlined above",
                btnAgree: "I Agree",
                mainTitle: "Emotion Rating Questionnaire",
                subTitle: "Please complete the following questionnaire",
                demoTitle: "Demographics",
                lblAge: "Age *",
                errAge: "Please enter your age.",
                lblGender: "Gender *",
                optSelect: "-- Select --",
                optMale: "Male",
                optFemale: "Female",
                optNonBinary: "Non-binary",
                optOther: "Other",
                errGender: "Please select your gender.",
                lblEdu: "Highest Education *",
                optEdu1: "Less than high school",
                optEdu2: "High school",
                optEdu3: "Some college",
                optEdu4: "Bachelor's degree",
                optEdu5: "Master's degree",
                optEdu6: "Doctoral degree",
                errEdu: "Please select your highest education level.",
                lblBefore: "Have you completed this questionnaire before? *",
                optYes: "Yes",
                optNo: "No",
                errBefore: "Please select an option.",
                soundTitle: "Sound Rating",
                soundInfo: "How many sounds would you like to rate?",
                lblNumSounds: "Number of Sounds *",
                btnLoad: "Load Sounds",
                ratingTitle: "Emotion Ratings",
                ratingInfo: "Listen to each sound and rate the emotions you feel. You can select 1 or 2 emotions per sound.",
                feedbackTitle: "Additional Feedback",
                lblFeedback: "Comments (Optional)",
                btnSubmit: "Submit Questionnaire",
                modalSave: "Make sure to save this code — you'll need it if you want your data deleted.",
                btnAnother: "Fill Another Questionnaire",
                // Dynamic rating texts
                dynSoundNum: "Sound #",
                dynPrimEmotion: "Primary Emotion *",
                dynPrimRating: "Primary Rating (1-5) *",
                tooltipRating: "Rate the strength of the emotion: 1 = weakest, 5 = strongest",
                dynSecEmotion: "Secondary Emotion (Optional)",
                dynSecRating: "Secondary Rating (1-5)",
                errPrimEmotion: "Please select a primary emotion.",
                errPrimRating: "Please provide a rating between 1 and 5.",
                errSecEmotion: "Please select a secondary emotion if you provided a rating.",
                errSecRating: "Please provide a rating between 1 and 5.",
                emoHappiness: "Happiness",
                emoSadness: "Sadness",
                emoAnger: "Anger",
                emoFear: "Fear",
                emoSurprise: "Surprise",
                emoDisgust: "Disgust",
                modalSubCode: "Your submission code:",
                modalSuccessMsg: "Submission successful! Thank you for your participation!" 
            },
            hu: {
                pageTitle: "Érzelem Értékelő Kérdőív",
                privacyTitle: "Adatvédelmi Szabályzat",
                privacyText: `
                    <p>Ön egy kutatásban vesz részt, amelyet <strong>Dr. Nagy Balázs</strong> (e-mail: <strong>nagybalazs@inf.elte.hu</strong>) koordinál. A kutatást magasan képzett szakemberek és asszisztenseik végzik. A vizsgálat célja hallásérzékelési adatok gyűjtése hangosztályozási és -értékelési kutatásokhoz, hogy elősegítse a hangfeldolgozó alkalmazások fejlesztését.</p>
                    <p><strong>A részvétel önkéntes.</strong> A hangminták meghallgatása és a kérdőívek kitöltése ártalmatlan, és nincsenek előre látható kockázatai. A kimerültség elkerülése érdekében a vizsgálat bármikor szüneteltethető. A résztvevők indoklás és következmények nélkül bármikor visszavonhatják hozzájárulásukat és megszakíthatják a részvételt. <strong>A részvételért pénzbeli juttatás nem jár.</strong></p>
                    <p>A vizsgálat során a résztvevőt arra kérjük, hogy hallgasson meg <strong>5-10 különböző hangmintát</strong> a saját hangeszközén keresztül, és értékelje azokat az online űrlap utasításai szerint, ami körülbelül <strong>10-15 percet</strong> vesz igénybe.</p>
                    <p>A vizsgálat eredményeit a későbbiekben <strong>publikációkban</strong> használják fel, valamint <strong>tudományos konferenciákon</strong> is bemutatják. Igény esetén írásbeli vagy szóbeli tájékoztatást nyújtunk ezekről az eseményekről.</p>
                    <p>A kutatás során gyűjtött minden információt (beleértve a videó- és/vagy hanganyagokat, ha azok a kutatás részét képezték) <strong>szigorúan bizalmasan</strong> kezelünk. A kutatás során nyert adatokat kódolt információként biztonságos számítógépen tároljuk, a papíralapú anyagokat (pl. kérdőíveket) pedig páncélszekrényben vagy zárt irodában tartjuk, szintén kódolt formában. Az egyedi kódokat a megbízott asszisztens biztosítja, és ezek csak számára hozzáférhetők és ismertek. A kutatás adatait statisztikailag elemezzük, amely során <strong>személyazonosításra nincs lehetőség.</strong> A személyes adatok kezelését szabályozó dokumentum (<strong>Általános Adatvédelmi Rendelet, GDPR</strong>) mellékleteivel együtt elérhető.</p>
                    <p>A vizsgálat eredményeiről <strong>nem készül orvosi vagy laboratóriumi jelentés.</strong> Kérésre szóbeli tájékoztatás nyújtható az eredményekről.</p>`,
                privacyCheckText: "Kérjük, olvassa el és jelölje be az összes négyzetet a folytatáshoz:",
                priv1: "Elolvastam és megértettem az online kutatásban való részvételemre vonatkozó információkat",
                priv2: "Elfogadom a feltételeket és hozzájárulok a kutatásban való részvételhez",
                priv3: "Hozzájárulok ahhoz, hogy a gyűjtött névtelen adatokat kutatási célokra felhasználják",
                priv4: "Megértettem, hogy bármikor megszakíthatom a részvételemet és az adataim kérésre törlésre kerülnek",
                priv5: "Megértettem, hogy az ELTE IK MI Tanszék bizalmasan kezeli a személyes adataimat",
                priv6: "Elolvastam és elfogadom az Adatvédelmi Tájékoztatót",
                privAll: "Elfogadom a fent leírt összes feltételt",
                btnAgree: "Elfogadom",
                mainTitle: "Érzelem Értékelő Kérdőív",
                subTitle: "Kérjük, töltse ki az alábbi kérdőívet",
                demoTitle: "Demográfia",
                lblAge: "Életkor *",
                errAge: "Kérjük, adja meg az életkorát.",
                lblGender: "Nem *",
                optSelect: "-- Válasszon --",
                optMale: "Férfi",
                optFemale: "Nő",
                optNonBinary: "Nem bináris",
                optOther: "Egyéb",
                errGender: "Kérjük, válassza ki a nemét.",
                lblEdu: "Legmagasabb iskolai végzettség *",
                optEdu1: "Általános iskola",
                optEdu2: "Középiskola",
                optEdu3: "Főiskola (befejezetlen)",
                optEdu4: "Alapképzés (BSc/BA)",
                optEdu5: "Mesterképzés (MSc/MA)",
                optEdu6: "Doktori fokozat (PhD)",
                errEdu: "Kérjük, válassza ki a végzettségét.",
                lblBefore: "Töltötte már ki ezt a kérdőívet korábban? *",
                optYes: "Igen",
                optNo: "Nem",
                errBefore: "Kérjük, válasszon egy opciót.",
                soundTitle: "Hang értékelése",
                soundInfo: "Hány hangot szeretne értékelni?",
                lblNumSounds: "Hangok száma *",
                btnLoad: "Hangok betöltése",
                ratingTitle: "Érzelem értékelések",
                ratingInfo: "Hallgassa meg a hangokat, és értékelje milyen érzést vált ki. Hangonként 1 vagy 2 érzelmet választhat.",
                feedbackTitle: "További visszajelzés",
                lblFeedback: "Megjegyzések (Opcionális)",
                btnSubmit: "Kérdőív beküldése",
                modalSave: "Kérjük, mentse el ezt a kódot — szüksége lesz rá, ha töröltetni szeretné az adatait.",
                btnAnother: "Új kérdőív kitöltése",
                // Dynamic rating texts
                dynSoundNum: "Hang #",
                dynPrimEmotion: "Elsődleges Érzelem *",
                dynPrimRating: "Elsődleges Értékelés (1-5) *",
                tooltipRating: "Értékelje az érzelem erősségét: 1 = leggyengébb, 5 = legerősebb",
                dynSecEmotion: "Másodlagos Érzelem (Opcionális)",
                dynSecRating: "Másodlagos Értékelés (1-5)",
                errPrimEmotion: "Kérjük, válasszon egy elsődleges érzelmet.",
                errPrimRating: "Kérjük, adjon meg egy értékelést 1 és 5 között.",
                errSecEmotion: "Kérjük, válasszon másodlagos érzelmet, ha értékelést adott meg.",
                errSecRating: "Kérjük, adjon meg egy értékelést 1 és 5 között.",
                emoHappiness: "Boldogság",
                emoSadness: "Szomorúság",
                emoAnger: "Harag",
                emoFear: "Félelem",
                emoSurprise: "Meglepetés",
                emoDisgust: "Undor",
                modalSubCode: "Az Ön beküldési kódja:",
                modalSuccessMsg: "Sikeres beküldés! Köszönjük a részvételt!"
            }
        };

        let currentLang = 'en';

        function setLanguage(lang) {
            currentLang = lang;
            
            // Update buttons
            document.getElementById('btn-en').classList.toggle('active', lang === 'en');
            document.getElementById('btn-hu').classList.toggle('active', lang === 'hu');

            // Update all static elements with data-i18n attribute
            document.querySelectorAll('[data-i18n]').forEach(element => {
                const key = element.getAttribute('data-i18n');
                if (translations[lang][key]) {
                    if (element.tagName === 'INPUT' && element.type === 'placeholder') {
                        element.placeholder = translations[lang][key];
                    } else {
                        element.innerHTML = translations[lang][key];
                    }
                }
            });

            // If sounds are loaded, we need to rebuild the ratings to translate them
            if (soundFiles.length > 0) {
                // Save current values before re-rendering
                const savedValues = saveCurrentFormValues();
                document.getElementById('emotionRatings').innerHTML = '';
                soundFiles.forEach((soundPath, index) => {
                    createRatingItem(index, soundPath);
                });
                restoreFormValues(savedValues);
                setupValidationListeners();
            }
        }

        function saveCurrentFormValues() {
            const formData = new FormData(document.getElementById('questionnaireForm'));
            let values = {};
            for (let [key, value] of formData.entries()) {
                values[key] = value;
            }
            return values;
        }

        function restoreFormValues(values) {
            for (let key in values) {
                let element = document.getElementById(key);
                if (element) {
                    element.value = values[key];
                }
            }
        }

        // Initialize language on load
        window.onload = () => setLanguage('en');

        // --- END TRANSLATION LOGIC ---

        const checkAll = document.getElementById('check-all');
        const checkboxes = document.querySelectorAll('.privacy-checkbox:not(#check-all)');
        const agreeButton = document.getElementById('agreeButton');
        const privacyPolicy = document.getElementById('privacyPolicy');
        const questionnaireBox = document.querySelector('.questionnaire-box');

        // When "Check All" is toggled
        checkAll.addEventListener('change', () => {
            const isChecked = checkAll.checked;
            checkboxes.forEach(checkbox => checkbox.checked = isChecked);
            updateAgreeButtonState();
        });

        // When a single checkbox is toggled
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkAll.checked = allChecked;
                updateAgreeButtonState();
            });
        });

        // When the agree button is clicked
        agreeButton.addEventListener('click', () => {
            privacyPolicy.style.display = 'none';
            questionnaireBox.style.display = 'block';
        });

        // Enable/disable the "I Agree" button based on all checkboxes
        function updateAgreeButtonState() {
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            agreeButton.disabled = !allChecked;
        }

        let soundFiles = [];
        let ratingCount = 0;

        // Function to clear all invalid states
        function clearInvalidStates() {
            document.querySelectorAll('.form-group.invalid').forEach(group => {
                group.classList.remove('invalid');
            });
            document.querySelectorAll('.radio-group.invalid').forEach(group => {
                group.classList.remove('invalid');
            });
        }

        // Function to highlight invalid field
        function highlightInvalidField(fieldId, isRadioGroup = false) {
            const group = document.getElementById(fieldId);
            if (group) {
                if (isRadioGroup) {
                    const radioGroup = group.querySelector('.radio-group');
                    if (radioGroup) {
                        radioGroup.classList.add('invalid');
                    }
                } else {
                    group.classList.add('invalid');
                }
            }
        }

        // Add input listeners to remove invalid state when user starts filling
        function addClearInvalidListeners() {
            // For text and number inputs
            document.querySelectorAll('input[type="text"], input[type="number"], select, textarea').forEach(element => {
                element.addEventListener('input', function () {
                    const formGroup = this.closest('.form-group');
                    if (formGroup) {
                        formGroup.classList.remove('invalid');
                    }
                });

                element.addEventListener('change', function () {
                    const formGroup = this.closest('.form-group');
                    if (formGroup) {
                        formGroup.classList.remove('invalid');
                    }
                });
            });

            // For radio buttons
            document.querySelectorAll('input[type="radio"]').forEach(element => {
                element.addEventListener('change', function () {
                    const radioGroup = this.closest('.radio-group');
                    if (radioGroup) {
                        radioGroup.classList.remove('invalid');
                    }
                });
            });
        }

        // Initialize listeners on page load
        addClearInvalidListeners();

        async function loadSounds() {
            const numSounds = document.getElementById('numSounds').value;
            const messageDiv = document.getElementById('message');

            try {
                // UPDATED: Changed to point to PHP API file
                const response = await fetch(`/api/get-sounds.php?count=${numSounds}`);
                const data = await response.json();

                if (!data.success) {
                    messageDiv.className = 'message error';
                    messageDiv.textContent = data.message || 'Error loading sounds.';
                    return;
                }

                soundFiles = data.sounds;

                if (soundFiles.length === 0) {
                    messageDiv.className = 'message error';
                    messageDiv.textContent = 'No sound files available.';
                    return;
                }

                // Hide sound selection section
                document.getElementById('soundSelectionSection').style.display = 'none';

                // Show emotion ratings section
                document.getElementById('emotionRatingsSection').style.display = 'block';
                document.getElementById('feedbackSection').style.display = 'block';
                document.getElementById('submitBtn').style.display = 'block';

                // Clear any existing ratings
                document.getElementById('emotionRatings').innerHTML = '';
                ratingCount = 0;

                // Create rating items for each sound
                soundFiles.forEach((soundPath, index) => {
                    createRatingItem(index, soundPath);
                });

                // Setup validation listeners
                setupValidationListeners();

                // Clear any previous messages
                messageDiv.style.display = 'none';

            } catch (error) {
                messageDiv.className = 'message error';
                messageDiv.textContent = 'Error loading sounds: ' + error.message;
            }
        }

        function createRatingItem(index, soundPath) {
            const t = translations[currentLang]; // Use current language
            const container = document.getElementById('emotionRatings');
            const newRating = document.createElement('div');
            newRating.className = 'emotion-rating-item';
            newRating.dataset.index = index;

            newRating.innerHTML = `
                <h3>${t.dynSoundNum}${index + 1}</h3>
                
                <div class="audio-player">
                    <audio id="audio_${index}" controls preload="metadata">
                        <source src="${soundPath}" type="audio/wav">
                    </audio>
                </div>
                
                <div class="form-group" id="emotion1-group-${index}">
                    <label for="emotion1_${index}">${t.dynPrimEmotion}</label>
                    <select id="emotion1_${index}" name="emotion1_${index}" class="emotion-select" data-index="${index}" data-type="primary" required>
                        <option value="">${t.optSelect}</option>
                        <option value="Happiness">${t.emoHappiness}</option>
                        <option value="Sadness">${t.emoSadness}</option>
                        <option value="Anger">${t.emoAnger}</option>
                        <option value="Fear">${t.emoFear}</option>
                        <option value="Surprise">${t.emoSurprise}</option>
                        <option value="Disgust">${t.emoDisgust}</option>
                    </select>
                    <div class="error-message">${t.errPrimEmotion}</div>
                </div>
                
                <div class="form-group" id="rating1-group-${index}">
                    <label for="rating1_${index}">
                        ${t.dynPrimRating}
                         <span class="info-icon" data-tooltip="${t.tooltipRating}">ⓘ</span>
                    </label>
                    <input type="number" id="rating1_${index}" name="rating1_${index}" class="rating-input" data-index="${index}" data-type="primary" min="1" max="5" step="1" required>
                    <div class="error-message">${t.errPrimRating}</div>
                </div>
                
                <div class="form-group" id="emotion2-group-${index}">
                    <label for="emotion2_${index}">${t.dynSecEmotion}</label>
                    <select id="emotion2_${index}" name="emotion2_${index}" class="emotion-select" data-index="${index}" data-type="secondary">
                        <option value="">${t.optSelect}</option>
                        <option value="Happiness">${t.emoHappiness}</option>
                        <option value="Sadness">${t.emoSadness}</option>
                        <option value="Anger">${t.emoAnger}</option>
                        <option value="Fear">${t.emoFear}</option>
                        <option value="Surprise">${t.emoSurprise}</option>
                        <option value="Disgust">${t.emoDisgust}</option>
                    </select>
                    <div class="error-message">${t.errSecEmotion}</div>
                </div>
                
                <div class="form-group" id="rating2-group-${index}">
                    <label for="rating2_${index}">${t.dynSecRating}</label>
                    <input type="number" id="rating2_${index}" name="rating2_${index}" class="rating-input" data-index="${index}" data-type="secondary" min="1" max="5" step="1">
                    <div class="error-message">${t.errSecRating}</div>
                </div>
            `;

            container.appendChild(newRating);
            ratingCount = index + 1;
        }

        function setupValidationListeners() {
            // For all emotion selects and rating inputs
            document.querySelectorAll('.emotion-select').forEach(select => {
                select.addEventListener('change', function () {
                    validateEmotionRating(this);
                    // Clear invalid state when user makes a selection
                    const formGroup = this.closest('.form-group');
                    if (formGroup) {
                        formGroup.classList.remove('invalid');
                    }
                });
            });

            document.querySelectorAll('.rating-input').forEach(input => {
                input.addEventListener('input', function () {
                    validateEmotionRating(this);
                    // Clear invalid state when user enters a value
                    const formGroup = this.closest('.form-group');
                    if (formGroup) {
                        formGroup.classList.remove('invalid');
                    }
                });
            });
        }

        function validateEmotionRating(element) {
            const index = element.dataset.index;
            const type = element.dataset.type;

            const emotionSelect = document.getElementById(`emotion${type === 'primary' ? '1' : '2'}_${index}`);
            const ratingInput = document.getElementById(`rating${type === 'primary' ? '1' : '2'}_${index}`);

            // If emotion is selected, make rating required
            if (emotionSelect.value) {
                ratingInput.required = true;
            } else {
                // If it's secondary, make it optional
                if (type === 'secondary') {
                    ratingInput.required = false;
                    ratingInput.value = ''; // Clear the value
                }
            }

            // If rating is entered, make emotion required (for secondary only)
            if (type === 'secondary' && ratingInput.value) {
                emotionSelect.required = true;
            } else if (type === 'secondary' && !ratingInput.value) {
                emotionSelect.required = false;
            }
        }

        // Modal logic
        function showSuccessModal(message, code) {
            const t = translations[currentLang];

            document.getElementById('modalMessage').innerHTML = t.modalSuccessMsg;
            document.getElementById('modalCode').innerHTML = code
                ? `<strong>${t.modalSubCode} ${code}</strong>`
                : '';
            document.getElementById('modalBackdrop').classList.add('active');
            document.getElementById('modalSuccess').classList.add('active');
        }

        function hideSuccessModal() {
            document.getElementById('modalBackdrop').classList.remove('active');
            document.getElementById('modalSuccess').classList.remove('active');
        }

        document.getElementById('fillAgainBtn').addEventListener('click', function () {
            hideSuccessModal();
            // Reset the form to initial state
            document.getElementById('questionnaireForm').reset();
            document.getElementById('soundSelectionSection').style.display = 'block';
            document.getElementById('emotionRatingsSection').style.display = 'none';
            document.getElementById('feedbackSection').style.display = 'none';
            document.getElementById('submitBtn').style.display = 'none';
            document.getElementById('emotionRatings').innerHTML = '';
            document.getElementById('message').textContent = '';
            document.getElementById('message').className = 'message';
            soundFiles = [];
            ratingCount = 0;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        document.getElementById('modalBackdrop').addEventListener('click', hideSuccessModal);

        document.getElementById('questionnaireForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            // Clear previous invalid states
            clearInvalidStates();

            const formData = new FormData(this);
            const messageDiv = document.getElementById('message');
            let hasErrors = false;

            // Validate demographics section
            if (!formData.get('age')) {
                highlightInvalidField('age-group');
                hasErrors = true;
            }

            if (!formData.get('gender')) {
                highlightInvalidField('gender-group');
                hasErrors = true;
            }

            if (!formData.get('highest_education')) {
                highlightInvalidField('education-group');
                hasErrors = true;
            }

            if (!formData.get('submitted_before')) {
                highlightInvalidField('submitted-before-group', true);
                hasErrors = true;
            }

            // Collect all emotion ratings with validation
            const results = [];
            const ratingItems = document.querySelectorAll('.emotion-rating-item');
            let validationError = false;
            let errorMessage = '';

            ratingItems.forEach(item => {
                const index = item.dataset.index;
                const emotion1 = formData.get(`emotion1_${index}`);
                const rating1 = formData.get(`rating1_${index}`);
                const emotion2 = formData.get(`emotion2_${index}`);
                const rating2 = formData.get(`rating2_${index}`);

                // Validate primary emotion
                if (!emotion1) {
                    highlightInvalidField(`emotion1-group-${index}`);
                    validationError = true;
                    hasErrors = true;
                    if (!errorMessage) {
                        errorMessage = `Please select a primary emotion for Sound #${parseInt(index) + 1}`;
                    }
                }

                // Validate primary rating
                if (!rating1 || rating1 < 1 || rating1 > 5) {
                    highlightInvalidField(`rating1-group-${index}`);
                    validationError = true;
                    hasErrors = true;
                    if (!errorMessage) {
                        errorMessage = `Please provide a valid rating (1-5) for the primary emotion in Sound #${parseInt(index) + 1}`;
                    }
                }

                // Validate primary emotion and rating combination
                if (emotion1 && !rating1) {
                    highlightInvalidField(`rating1-group-${index}`);
                    validationError = true;
                    hasErrors = true;
                    if (!errorMessage) {
                        errorMessage = `Please provide a rating for the primary emotion in Sound #${parseInt(index) + 1}`;
                    }
                }

                // Validate secondary emotion and rating
                if (emotion2 && !rating2) {
                    highlightInvalidField(`rating2-group-${index}`);
                    validationError = true;
                    hasErrors = true;
                    if (!errorMessage) {
                        errorMessage = `Please provide a rating for the secondary emotion in Sound #${parseInt(index) + 1}`;
                    }
                }

                if (rating2 && !emotion2) {
                    highlightInvalidField(`emotion2-group-${index}`);
                    validationError = true;
                    hasErrors = true;
                    if (!errorMessage) {
                        errorMessage = `Please select a secondary emotion for Sound #${parseInt(index) + 1}`;
                    }
                }

                if (emotion1 && rating1) {
                    // Get the sound file path for this rating
                    const soundPath = soundFiles[index];
                    const sound_code = soundPath.split(/[/\\]/).pop().replace(/\.wav$/i, '');

                    results.push({
                        sound_code: sound_code,
                        emotion1: emotion1,
                        rating1: rating1,
                        emotion2: emotion2 || null,
                        rating2: rating2 || null
                    });
                }
            });

            // Stop if validation failed
            if (hasErrors) {
                messageDiv.className = 'message error';
                messageDiv.textContent = errorMessage || 'Please fill in all required fields.';
                // Scroll to first invalid field
                const firstInvalid = document.querySelector('.form-group.invalid, .radio-group.invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            // Prepare submission data
            const data = {
                age: formData.get('age'),
                gender: formData.get('gender'),
                highest_education: formData.get('highest_education'),
                submitted_before: formData.get('submitted_before') === 'true',
                feedback: formData.get('feedback'),
                results: results
            };

            try {
                // UPDATED: Changed to point to PHP API file
                const response = await fetch('/api/submit.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    // Show modal instead of inline message
                    showSuccessModal(result.message, result.code);

                    // Reset the form (state for next time)
                    document.getElementById('questionnaireForm').reset();
                    document.getElementById('soundSelectionSection').style.display = 'block';
                    document.getElementById('emotionRatingsSection').style.display = 'none';
                    document.getElementById('feedbackSection').style.display = 'none';
                    document.getElementById('submitBtn').style.display = 'none';
                    document.getElementById('emotionRatings').innerHTML = '';
                    soundFiles = [];
                    ratingCount = 0;

                } else {
                    messageDiv.className = 'message error';
                    messageDiv.textContent = 'Error: ' + result.message;
                }
            } catch (error) {
                messageDiv.className = 'message error';
                messageDiv.textContent = 'An error occurred. Please try again.';
            }
        });

    </script>
</body>

</html>