import React from "react";
import Transactions from "./Transactions/Transactions";
import InfoCard from "./InfoCard";
import InvoiceCard from "./InvoiceCard";


export default class Dashboard extends React.Component {
    render() {
        return (
            <div>
                <div className="row">
                    <div className="col-lg-6 col-xl-5 col-md-8 col-12">
                        <InvoiceCard/>
                    </div>
                    <div className="col-lg-4 col-md-4 d-md-block d-none">
                        <InfoCard />
                    </div>
                </div>
                <Transactions/>
            </div>
        );
    }
}