import React from "react";

export default class InvoiceCard extends React.Component {
    render() { return (
        <div className="invoice card card-body">
            <form name="new_invoice" method="post" action="#">
                <label className="form-control-label"
                       htmlFor="new_invoice_amount">Amount</label>
                <div className="form-group row">
                    <div className="col-9 col-md-10">

                        <div className="input-group input-group-lg">
                            <input className="form-control form-control-lg"
                                   id="new_invoice_amount" placeholder="0,00"
                                   type="number" />
                            <div className="input-group-append">
                                <div className="input-group-text">NOK</div>
                            </div>
                        </div>

                        <div className="help-block text-right" style={{paddingRight: '16px',paddingTop: '8px'}}>0.3494
                            µBTC
                        </div>


                    </div>

                    <div className="col-3 col-md-2 text-center" style={{paddingTop: '9px'}}>
                        <button type="submit"
                                className="btn-secondary raised btn btn-lg btn-round btn-pink">
                            <i className="fa fa-check"/>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    )}
}