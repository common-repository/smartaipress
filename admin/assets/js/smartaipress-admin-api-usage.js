(function ($, window, document) {
    'use strict';
    // execute when the DOM is ready
    $(document).ready(function () {

        /**
         * Global chart properties
         */
        let chartInstance;
        let currentLimit;
        let currentFromDate;
        let currentToDate;
        let chartLoaderTimer = 5;

        /**
         * Fetch OpenAI API usage data and display it in a chart or show an error message.
         */
        function checkOpenaiApiUsage(limit = 5, from = '', to = '') {
            $.post(openai_api.url, {
                action: "smartaipress_get_openai_api_usage_data",
                limit: limit,
                from: from,
                to: to,
                _ajax_nonce: openai_api.nonce   
            }, function(data) {
                const details = JSON.parse(data);
                const mainChartContainer = document.getElementById("smartaipress-openai-chart-container");
                const ctx = document.getElementById('openAiApiUsageChart');
                
                if (details.error) {
                    const errorMsg = `<div class="smartaipress-alert smartaipress-alert-primary">Info! ${details.message}</div>`;
                    if (mainChartContainer) {
                        mainChartContainer.innerHTML = errorMsg;
                    }
                } else {
                    createOpenAiApiUsageChart(details.usage_content, ctx);
                    currentLimit = limit;
                    currentFromDate = from;
                    currentToDate = to;
                }
            });
        }

        checkOpenaiApiUsage();

        let totalDaysError = document.getElementById("total-days-error");
        let datesErrorBox = document.getElementById("dates-error-box");

        /**
         * Filter api usage data by defining number of days
         */
        const filterByDayBtn = document.getElementById("filter-by-day-btn");
        const totalDaysFilter = document.getElementById("total-days-filter");

        filterByDayBtn.addEventListener("click", function() {
            let totalDays = totalDaysFilter.value;

            if(currentLimit == totalDays) {
                totalDaysError.innerHTML = openai_api.ajax_data.chart_days_data_already_displayed;
                return;
            }

            if(totalDays <= 0) {
                totalDaysError.innerHTML = openai_api.ajax_data.chart_day_filter;
                return;
            }
            
            if(totalDays > 365) {
                totalDaysError.innerHTML = openai_api.ajax_data.chart_days_maximum;
                return;
            }

            if(totalDays >= 100 && totalDays <= 250) {
                chartLoaderTimer = 10;
            }

            if(totalDays > 250) {
                chartLoaderTimer = 15;
            }

            datesErrorBox.innerHTML = '';
            totalDaysError.innerHTML = '';
            checkOpenaiApiUsage(totalDays);
        });

        /**
         * Calculates total days between two dates.
         * @param {String} endDate - The end date
         * @param {String} startDate - The start date
         * @returns {Number} - Total days between two dates.
         */
        function calculateDateDifference(endDate, startDate) {
            let timeDifference = new Date(endDate) - new Date(startDate);
            let daysDifference = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));
            return daysDifference;
        }

        /**
         * Filter api usage data by defining dates
         */
        const filterByDatesBtn = document.getElementById("filter-by-dates-btn");
        const fromDateFragment = document.getElementById("from-date-fragment");
        const toDateFragment = document.getElementById("to-date-fragment");

        filterByDatesBtn.addEventListener("click", function() {
            let fromDate = fromDateFragment.value;
            let toDate = toDateFragment.value;
            let currentDateFragment = getCurrentDateFragment();
            let minDate = '2023-01-01';

            if(fromDate == '' || toDate == '') {
                datesErrorBox.innerHTML = openai_api.ajax_data.chart_dates_defined;
                return;
            }

            if(fromDate > currentDateFragment || toDate > currentDateFragment) {
                datesErrorBox.innerHTML = openai_api.ajax_data.chart_date_in_future;
                return;
            }

            if(fromDate < minDate || toDate < minDate) {
                datesErrorBox.innerHTML = openai_api.ajax_data.chart_date_in_past + minDate;
                return;
            }

            if(fromDate > toDate) {
                datesErrorBox.innerHTML = openai_api.ajax_data.from_bigger_than_to;
                return;
            }

            if(fromDate == currentFromDate && toDate == currentToDate) {
                datesErrorBox.innerHTML = openai_api.ajax_data.chart_dates_already_displayed;
                return;
            }

            let dayDifferenceFragment = calculateDateDifference(toDate, fromDate);

            if(dayDifferenceFragment > 365) {
                datesErrorBox.innerHTML = openai_api.ajax_data.chart_days_maximum;
                return;
            }

            if(dayDifferenceFragment >= 100 && dayDifferenceFragment <= 250) {
                chartLoaderTimer = 10;
            }

            if(dayDifferenceFragment > 250) {
                chartLoaderTimer = 15;
            }

            totalDaysError.innerHTML = '';
            datesErrorBox.innerHTML = '';
            checkOpenaiApiUsage('', fromDate, toDate);
        });

        /**
         * Get current date, format is: YYYY-MM-DD
         */
        function getCurrentDateFragment() {
            // Create a new Date object
            const currentDate = new Date();
          
            // Get the individual components of the date
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth() + 1; // Month is zero-indexed, so we add 1
            const day = currentDate.getDate();
          
            // Format the date as a string (you can customize the format as needed)
            const formattedDate = `${year}-${month < 10 ? '0' : ''}${month}-${day < 10 ? '0' : ''}${day}`;
          
            return formattedDate;
        }

        /**
         * Creates and initializes an OpenAI API Usage Chart using the provided data.
         *
         * @param {string} data - A JSON-encoded dataset containing usage details.
         */
        function createOpenAiApiUsageChart(data, ctx) {
            const details = JSON.parse(data);
            const formattedDates = details.map(detail => getCurrentDate(Date.parse(detail.dayFragment) / 1000));
        
            const allDatasets = [
                { label: 'Image Models', data: [], borderWidth: 1 },
                { label: 'Audio Models', data: [], borderWidth: 1 },
                { label: 'GPT-4', data: [], borderWidth: 1 },
                { label: 'GPT-4 Turbo', data: [], borderWidth: 1 },
                { label: 'GPT-3.5 Turbo', data: [], borderWidth: 1 }
            ];

            let $loader = $('.sap-loader-box');
            $loader.css('display', 'flex');
        
            details.forEach(detail => {
                const record = JSON.parse(detail.dataFragments);
                allDatasets[0].data.push(totalDalleUsage(record.dalle_api_data)); // Image Models
                allDatasets[1].data.push(totalWhisperUsage(record.whisper_api_data)); // Audio Models
                allDatasets[2].data.push(totalGpt4TextModelsUsage(record.data)); // GPT-4
                allDatasets[3].data.push(totalGpt4TurboTextModelsUsage(record.data)); // GPT-4 Turbo
                allDatasets[4].data.push(totalGpt35TextModelsUsage(record.data)); // GPT-3.5
            });

            setTimeout(function() {
                $loader.css('display', 'none');
                
                if(chartInstance) {
                    chartInstance.destroy();
                }
    
                chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: formattedDates,
                        datasets: allDatasets
                    },
                    options: {
                        plugins: {
                            title: {
                                display: true,
                                text: 'OpenAI API Usage Chart'
                            },
                            tooltip: {
                                callbacks: {
                                    label: context => {
                                        let label = context.dataset.label || '';
                                        label += ': ';
                                        label += context.parsed.y < 0.01 ? '<$0.01' : new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                        return label;
                                    }
                                }
                            }
                        },
                        responsive: true,
                        indexAxis: 'x',
                        scales: {
                            x: { stacked: true },
                            y: {
                                stacked: true,
                                beginAtZero: true,
                                ticks: { callback: value => '$' + value }
                            }
                        }
                    }
                });

            }, chartLoaderTimer * 1000);
        }

        /**
         * Converts a Unix timestamp to a formatted date string in the 'day month' format.
         *
         * @param {number} timeFragment - The Unix timestamp to be converted.
         * @returns {string} The formatted date string, e.g., '01 January'.
         */
        function getCurrentDate(timeFragment) {
            const date = new Date(timeFragment * 1000);
            const options = { day: '2-digit', month: 'long' };
            return date.toLocaleDateString('en-US', options);
        }

        /**
         * Calculate the total price for a DALL·E usage based on the provided data.
         * @param {Array} dalleData - An array of objects containing image size and the number of images.
         * @returns {string} - The total price formatted to two decimal places.
         */
        function totalDalleUsage(dalleData) {
            const priceMap = {
                256: 0.016,
                512: 0.018,
                1024: 0.020
            };
            let price = 0;

            for (const data of dalleData) {
                const imgWidth = Number(data.image_size.split('x')[0]);
                price += (priceMap[imgWidth] || 0) * data.num_images;
            }

            return price.toFixed(2);
        }

        /**
         * Calculate the total price for Whisper usage based on the provided data.
         * @param {Array} whisperData - An array of objects containing the duration in seconds.
         * @returns {number} - The total price, rounded to two decimal places.
         */
        function totalWhisperUsage(whisperData) {
            const pricePerMinute = 0.006;
            const totalLength = whisperData.reduce((total, data) => total + data.num_seconds, 0);
            return parseFloat((totalLength / 60 * pricePerMinute).toFixed(2));
        }

        /**
         * Calculate the total price for GPT-4 Text Models usage based on the provided data.
         * @param {Array} textData - An array of objects containing information about text models.
         * @returns {number} - The total price for GPT-4 Text Models usage.
         */
        function totalGpt4TextModelsUsage(textData) {
            const priceContext = 0.03 / 1000; // Price per context token
            const priceGenerated = 0.06 / 1000; // Price per generated token

            let totalGpt4ContextTokens = 0;
            let totalGpt4GeneratedTokens = 0;

            for (const data of textData) {
                if (data.snapshot_id.includes("gpt-4") && data.snapshot_id != "gpt-4-1106-preview") {
                    totalGpt4ContextTokens += data.n_context_tokens_total;
                    totalGpt4GeneratedTokens += data.n_generated_tokens_total;
                }
            }

            return (totalGpt4ContextTokens * priceContext + totalGpt4GeneratedTokens * priceGenerated);
        }

        /**
         * Calculate the total price for GPT-4 Text Models usage based on the provided data.
         * @param {Array} textData - An array of objects containing information about text models.
         * @returns {number} - The total price for GPT-4 Text Models usage.
         */
        function totalGpt4TurboTextModelsUsage(textData) {
            const priceContext = 0.01 / 1000; // Price per context token
            const priceGenerated = 0.03 / 1000; // Price per generated token

            let totalGpt4TurboContextTokens = 0;
            let totalGpt4TurboGeneratedTokens = 0;

            for (const data of textData) {
                if (data.snapshot_id === "gpt-4-1106-preview") {
                    totalGpt4TurboContextTokens += data.n_context_tokens_total;
                    totalGpt4TurboGeneratedTokens += data.n_generated_tokens_total;
                }
            }

            return (totalGpt4TurboContextTokens * priceContext + totalGpt4TurboGeneratedTokens * priceGenerated);
        }

        /**
         * Calculate the total price for GPT-3.5 Text Models usage based on the provided data.
         * @param {Array} textData - An array of objects containing information about text models.
         * @returns {number} - The total price for GPT-3.5 Text Models usage.
         */
        function totalGpt35TextModelsUsage(textData) {
            const priceContext = 0.0015 / 1000; // Price per context token
            const priceGenerated = 0.002 / 1000; // Price per generated token

            let totalGpt35ContextTokens = 0;
            let totalGpt35GeneratedTokens = 0;

            for (const data of textData) {
                if (data.snapshot_id.includes("gpt-3.5")) {
                    totalGpt35ContextTokens += data.n_context_tokens_total;
                    totalGpt35GeneratedTokens += data.n_generated_tokens_total;
                }
            }

            return (totalGpt35ContextTokens * priceContext + totalGpt35GeneratedTokens * priceGenerated);
        }

    });
}(jQuery, window, document));
