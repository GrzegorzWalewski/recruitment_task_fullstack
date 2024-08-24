import React, {Component} from 'react';
import axios from 'axios';
import SimpleDatePicker from './Calendar';

class ExchangeRates extends Component {
    constructor(props) {
        super(props);
        this.state = {
            selectedDate: '',
            rates: [],
            loading: true,
            errorMessage: ''
        };
    }

    handleDateChange(date) {
        if (date !== this.state.selectedDate) {
            this.updateDateInUrl(date);
            this.getExchangeRates(date);
            this.setState({ selectedDate: date });
        }
    }

    updateDateInUrl(date) {
        const currentUrl = window.location.href;
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('date', date);
        const newUrl = `${currentUrl.split('?')[0]}?${urlParams.toString()}`;
        window.history.pushState({ path: newUrl }, '', newUrl);
    }

    getBaseUrl() {
        return 'http://telemedi-zadanie.localhost';
    }

    componentDidMount() {
        const urlParams = new URLSearchParams(window.location.search);
        const dateParam = urlParams.get('date');

        if (dateParam !== null) {
            this.getExchangeRates();
            this.setState({ selectedDate: dateParam });
            this.getExchangeRates(dateParam);
        }
    }

    getExchangeRates(date = '') {
        this.setState({ loading: true });
        this.setState({ errorMessage: '' });

        const baseUrl = this.getBaseUrl();

        axios.get(baseUrl + '/api/exchange-rates/' + date).then(response => {
            if (date === '') {
                this.updateRates(response.data, true);
            } else {
                this.updateRates(response.data);
            }
        }).catch((error) => {
            this.setState({ errorMessage: error.response.data.message });
        }).finally(() => {
            this.setState({ loading: false });
        });
    }

    updateRates(rates, today = false) {
        var prefix = '';

        if (today) {
            prefix = 'today_';
        }

        var stateRates = this.state.rates;

        for (var key in rates) {
            var rate = {
                [prefix + 'buy']: rates[key]['buy'],
                [prefix + 'sell']: rates[key]['sell']
            }

            if (rates[key]['name']) {
                rate['name'] = rates[key]['name'];
            }

            if (stateRates[rates[key]['code']]) {
                rate = {...stateRates[rates[key]['code']], ...rate};
            }

            stateRates[rates[key]['code']] = rate;
        }

        this.setState({ rates: stateRates });
    }

    shouldComponentUpdate(nextProps, nextState) {
        return true;
    }

    render() {
        const ratesList = [];

        for (var key in this.state.rates) {
            ratesList.push(
                <tr key={key}>
                    <td>{this.state.rates[key].name}</td>
                    <td>{key}</td>
                    <td>{this.state.rates[key].buy}</td>
                    <td>{this.state.rates[key].today_buy}</td>
                    <td>{this.state.rates[key].sell}</td>
                    <td>{this.state.rates[key].today_sell}</td>
                </tr>
            );
        }

        return(
            <div>
                <section className="row-section">
                    <div className="container">
                        <div className="row mt-5">
                            <div className="col-md-8 offset-md-2">
                                <h2 className="text-center">
                                    Exchange Rates
                                </h2>
                                <SimpleDatePicker selectedDate={this.state.selectedDate} setSelectedDate={this.handleDateChange.bind(this)}/>
                                {this.state.errorMessage ? (<p className="text-danger">{this.state.errorMessage}</p>)
                                : this.state.loading || !this.state.selectedDate ? (
                                    <div className={'text-center'}>
                                        {!this.state.selectedDate ? (
                                            <span className='text-info'>Choose date to get exchange rates</span>
                                        ): (
                                            <span className="fa fa-spin fa-spinner fa-4x"></span>
                                        )}
                                    </div>
                                ) : (
                                <table className="table table-responsive table-bordered border-dark p-4">
                                    <thead>
                                        <tr>
                                            <th colSpan="2">Currency</th>
                                            <th colSpan="2">Buy</th>
                                            <th colSpan="2">Sell</th>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>{this.state.selectedDate}</th>
                                            <th>Today</th>
                                            <th>{this.state.selectedDate}</th>
                                            <th>Today</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {ratesList}
                                    </tbody>
                                </table>
                                )}
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        )
    }
}
export default ExchangeRates;
