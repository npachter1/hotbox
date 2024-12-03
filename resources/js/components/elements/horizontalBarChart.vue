<script>
    import { HorizontalBar } from 'vue-chartjs';

    export default {
        extends: HorizontalBar,
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