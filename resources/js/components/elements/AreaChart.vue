<script>
    import { Line } from 'vue-chartjs';

    export default {
        extends: Line,
        name: 'AreaChart',
        props:['chartData','options'],
        methods: {
            update() {
                this.$data._chart.update();
            }
        },
        mounted () {
            this.renderChart(this.chartData, this.options)
        }
    };
</script>