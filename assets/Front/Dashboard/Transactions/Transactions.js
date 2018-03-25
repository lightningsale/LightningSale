import React from "react";
import TransactionItem from "./TransactionItem";

export default class Transactions extends React.Component {
    render() {
        return (
            <div className="col-12 transaction-list">
                <h1>Transactions
                    <span className="pull-right"><i className="fa fa-angle-up"/></span>
                </h1>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
                <TransactionItem/>
            </div>
        )
    }
}