const { ChartJSNodeCanvas } = require('chartjs-node-canvas');
const fs = require('fs');

const width = 800; // ancho del gráfico
const height = 600; // alto del gráfico
const chartJSNodeCanvas = new ChartJSNodeCanvas({ width, height });

const generarGrafico = async (chartData, outputFile) => {
    const configBarChart = {
        type: 'bar',
        data: {
            labels: Object.keys(chartData),
            datasets: [{
                label: 'Frecuencia',
                data: Object.values(chartData),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    const buffer = await chartJSNodeCanvas.renderToBuffer(configBarChart);
    fs.writeFileSync(outputFile, buffer);
};

const main = async () => {
    const data = {"4":1,"3":1,"8":3,"10":2,"9":2,"7":2,"6":2,"5":2}; // ejemplo de datos
    await generarGrafico(data, 'chart.png');
};

main();