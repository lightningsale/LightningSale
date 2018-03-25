import React from "react";

export default class TransactionItem extends React.Component {
    render() {
        return (
            <div className="transaction-list-item row">
                <div className="col-2 col-sm-2 col-md-2">
                    <button className="btn btn-lg btn-outline-dark"><i
                        className="fa fa-qrcode"/></button>
                </div>
                <div className="col-4 col-sm-5 col-md-3">
                    <div
                        style={{minWidth: "5em", maxWidth: "10em", flex: 3, overflow: "hidden", textOverflow: "ellipsis"}}>lntb3727140n1pd9k7fppp5lv2t3m5uje0kt747tc943ndlntb3nd...
                    </div>
                    <div className="small"><a href="#" className="text-pink">Copy</a></div>
                    <div
                        className="d-block d-md-none transaction-sub-item">14.01.2018&nbsp;15:37
                    </div>
                </div>
                <div className="col-6 col-sm-5 col-md-3 text-right">
                    <div className="">396,30 NOK</div>
                    <div className="small">0.5194 µBTC</div>
                    <div
                        className="text-pink d-block d-md-none transaction-sub-item">Waiting
                    </div>
                </div>
                <div className="d-none col-sm-3 col-md-4 text-right d-md-block">
                    <div className="text-pink">Waiting</div>
                    <div className="small">14.01.2018&nbsp;15:37</div>
                </div>
            </div>
        )
    }
}